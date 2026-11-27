<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Exception;
use InvalidArgumentException;
use Laminas\Validator\Regex;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\FormData;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;
use Smile\ScopedEav\ViewModel\Data as DataViewModel;

/**
 * Scoped EAV entity attribute save controller.
 */
class Save extends AbstractAttribute implements HttpPostActionInterface
{
    /**
     * Structural fields never taken from the request: they are computed on creation and frozen afterwards.
     * Model fields hold class names instantiated by Magento, so accepting them would allow arbitrary instantiation.
     * The id and the set assignment fields would let the request target another attribute or attribute set.
     */
    private const PROTECTED_FIELDS = [
        'attribute_code',
        'entity_type_id',
        'is_user_defined',
        'backend_type',
        'backend_model',
        'backend_table',
        'source_model',
        'frontend_model',
        'attribute_model',
        'attribute_id',
        'attribute_set_id',
        'attribute_group_id',
        'sort_order',
    ];

    private DataViewModel $dataViewModel;

    private FormData $formDataSerializer;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param DataViewModel $dataViewModel Scoped EAV data view model.
     * @param BuilderInterface $attributeBuilder Attribute builder.
     * @param FormData $formDataSerializer Serializer.
     * @param ForwardFactory $resultForwardFactory Forward factory.
     */
    public function __construct(
        Context $context,
        DataViewModel $dataViewModel,
        BuilderInterface $attributeBuilder,
        FormData $formDataSerializer,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $dataViewModel, $attributeBuilder, $resultForwardFactory);
        $this->dataViewModel = $dataViewModel;
        $this->formDataSerializer = $formDataSerializer;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $attribute = $this->getAttribute();
            $attribute = $this->addPostData($attribute);

            /** @var \Smile\ScopedEav\Model\Entity\Attribute $attribute */
            $attribute->save();

            $response = $this->resultRedirectFactory->create();

            if ($this->getRequest()->getParam('back', false)) {
                $response->setPath(
                    "*/*/edit",
                    ['attribute_id' => $attribute->getId(), '_current' => true]
                );
            } else {
                $response->setPath('*/*/index');
            }
        } catch (Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }

    /**
     * Add request post data to the attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     * @throws \Exception
     */
    private function addPostData(AttributeInterface $attribute): AttributeInterface
    {
        try {
            $optionData = $this->formDataSerializer
                ->unserialize($this->getRequest()->getParam('serialized_options', '[]'));
        } catch (InvalidArgumentException $e) {
            $message = (string) __("The attribute couldn't be saved due to an error."
                . " Verify your information and try again. If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);
            // @phpstan-ignore-next-line
            return $this->returnResult('catalog/*/edit', ['_current' => true], ['error' => true]);
        }

        /** @var Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        $data = array_replace_recursive(
            $data,
            $optionData
        );

        // Structural fields must never come from the request.
        foreach (self::PROTECTED_FIELDS as $field) {
            unset($data[$field]);
        }

        /** @var \Smile\ScopedEav\Model\Entity\Attribute $attribute */
        if (!$attribute->getId()) {
            $data['attribute_code']  = $this->getAttributeCode();
            $data['is_user_defined'] = true;
            $data['backend_type'] = $this->dataViewModel->getAttributeBackendTypeByInput(
                (string) $data['frontend_input']
            ) ?: $attribute->getBackendTypeByInput($data['frontend_input']);

            $data['source_model'] = $this->dataViewModel->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_model'] = $this->dataViewModel->getAttributeBackendModelByInputType($data['frontend_input']);
        } else {
            // The input type is read-only in the edit form: changing it would desync backend type and models.
            unset($data['frontend_input']);
        }

        $data = $this->filterOptionData($data, $attribute);

        $frontendInput = $data['frontend_input'] ?? $attribute->getFrontendInput();
        $defaultValueField = $attribute->getDefaultValueByInput($frontendInput);
        if ($defaultValueField) {
            $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
        }

        $attribute->addData($data);

        /** @var AttributeInterface $attribute */
        return $attribute;
    }

    /**
     * Remove from the posted options the ids that do not belong to the attribute.
     *
     * Core updates and deletes options by option_id alone, so a forged id would reach another attribute's options.
     *
     * @param array $data Post data.
     * @param AbstractModel $attribute Attribute.
     */
    private function filterOptionData(array $data, AbstractModel $attribute): array
    {
        if (!isset($data['option']) || !is_array($data['option'])) {
            unset($data['option']);

            return $data;
        }

        $ownOptionIds = [];
        if ($attribute->getId()) {
            /** @var AbstractDb $resource */
            $resource = $attribute->getResource();
            $connection = $resource->getConnection();
            $select = $connection->select()
                ->from($resource->getTable('eav_attribute_option'), 'option_id')
                ->where('attribute_id = ?', (int) $attribute->getId());
            $ownOptionIds = array_map('intval', $connection->fetchCol($select));
        }

        foreach ($data['option'] as $key => $rows) {
            if (!is_array($rows)) {
                unset($data['option'][$key]);
                continue;
            }
            foreach (array_keys($rows) as $optionId) {
                // Non numeric ids (option_0, ...) are new options, core inserts them for the current attribute.
                if (is_numeric($optionId) && !in_array((int) $optionId, $ownOptionIds, true)) {
                    unset($data['option'][$key][$optionId]);
                }
            }
        }

        return $data;
    }

    /**
     * Generate attribute code from request params.
     *
     * @throws \Exception
     */
    private function getAttributeCode(): string
    {
        $attributeCode = $this->getRequest()->getParam('attribute_code');

        if (!$attributeCode) {
            $attributeCode = $this->generateCode($this->getRequest()->getParam('frontend_label')[0]);
        }

        if (trim($attributeCode) == '') {
            /** @var Regex $validatorAttrCode */
            $validatorAttrCode = new Regex('/^[a-z][a-z_0-9]{0,30}$/');

            if (!$validatorAttrCode->isValid($attributeCode)) {
                $errorMessageParts = [
                    __('Attribute code "%1" is invalid.', $attributeCode),
                    __('Please use only letters (a-z), numbers (0-9) or underscore(_) in this field.'),
                    __('First character should be a letter.'),
                ];
                throw new LocalizedException(__(implode(' ', $errorMessageParts), $attributeCode));
            }
        }

        return $attributeCode;
    }
}
