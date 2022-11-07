<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

use Magento\Catalog\Model\Category\FileInfo;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Model\AbstractEntity;
use Smile\ScopedEav\Model\Locator\LocatorInterface;
use Smile\ScopedEav\Ui\DataProvider\Entity\Form\EavValidationRules;

/**
 * Scoped EAV attribute form modifier.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Eav extends AbstractModifier
{
    private Helper\Eav $eavHelper;

    private LocatorInterface $locator;

    private ArrayManager $arrayManager;

    private EavValidationRules $validationRules;

    private DataPersistorInterface $dataPersistor;

    private FileInfo $fileInfo;

    private EavAttributeFactory $eavAttributeFactory;

    /**
     * @var array
     */
    private array $bannedInputTypes;

    /**
     * @var array
     */
    private array $attributesToEliminate;

    /**
     * @var array
     */
    private array $attributesToDisable;

    /**
     * @var array
     */
    private array $attributesCache = [];

    /**
     * Constructor.
     *
     * @param Helper\Eav $eavHelper EAV helper.
     * @param LocatorInterface $locator Entity locator.
     * @param ArrayManager $arrayManager Array manager.
     * @param EavValidationRules $validationRules EAV validation rules
     * @param DataPersistorInterface $dataPersistor Data persistor.
     * @param FileInfo $fileInfo File information.
     * @param array $attributesToEliminate Attribute codes removed from the form.
     * @param EavAttributeFactory $eavAttributeFactory Eav attribute factory.
     * @param array $attributesToEliminate Attribute codes removed from the form.
     * @param array $attributesToDisable Attribute codes to be disabled.
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Helper\Eav $eavHelper,
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        EavValidationRules $validationRules,
        DataPersistorInterface $dataPersistor,
        FileInfo $fileInfo,
        EavAttributeFactory $eavAttributeFactory,
        array $bannedInputTypes = [],
        array $attributesToEliminate = [],
        array $attributesToDisable = []
    ) {
        $this->eavHelper = $eavHelper;
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->bannedInputTypes = $bannedInputTypes;
        $this->validationRules = $validationRules;
        $this->dataPersistor = $dataPersistor;
        $this->attributesToEliminate = $attributesToEliminate;
        $this->attributesToDisable = $attributesToDisable;
        $this->fileInfo = $fileInfo;
        $this->eavAttributeFactory = $eavAttributeFactory;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        if (!$this->locator->getEntity()->getId() && $this->dataPersistor->get('entity')) {
            return $this->resolvePersistentData($data);
        }

        $entityId = $this->locator->getEntity()->getId();

        foreach (array_keys($this->getGroups()) as $groupCode) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            foreach ($attributes as $attribute) {
                $attributeValue = $this->setupAttributeData($attribute);
                if (null !== $attributeValue) {
                    $attributeValue = $this->overrideImageUploaderData($attribute, $attributeValue);
                    $data[$entityId][self::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $attributeValue;
                }
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = 0;

        foreach ($this->getGroups() as $groupCode => $group) {
            /** @var array $attributes */
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['label'] = __('%1', $group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = false;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_ENTITY;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] =
                    $sortOrder * self::SORT_ORDER_MULTIPLIER;
            }

            $sortOrder++;
        }

        return $meta;
    }

    /**
     * Build attribute container.
     *
     * @param AttributeInterface $attribute Attribute.
     * @return array
     */
    public function setupAttributeContainerMeta(AttributeInterface $attribute): array
    {
        $containerMeta = $this->arrayManager->set(
            self::META_CONFIG_PATH,
            [],
            [
                'formElement'   => Container::NAME,
                'componentType' => Container::NAME,
                'breakLine'     => false,
                'label'         => $attribute->getDefaultFrontendLabel(),
                'required'      => $attribute->getIsRequired(),
            ]
        );

        /** @var EavAttribute $attribute */
        if ($attribute->getIsWysiwygEnabled()) {
            $containerMeta = $this->arrayManager->merge(
                self::META_CONFIG_PATH,
                $containerMeta,
                ['component' => 'Magento_Ui/js/form/components/group']
            );
        }

        return $containerMeta;
    }

    /**
     * Add attribute container children.
     *
     * @param array $attributeContainer Attribute container.
     * @param AttributeInterface $attribute Attibute.
     * @param string $groupCode Group code.
     * @param int $sortOrder Attribute sort order.
     * @return array
     */
    public function addContainerChildren(
        array $attributeContainer,
        AttributeInterface $attribute,
        string $groupCode,
        int $sortOrder
    ): array {
        foreach ($this->getContainerChildren($attribute, $groupCode, $sortOrder) as $childCode => $child) {
            $attributeContainer['children'][$childCode] = $child;
        }

        $attributeContainer = $this->arrayManager->merge(
            self::META_CONFIG_PATH,
            $attributeContainer,
            ['sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER, 'scopeLabel' => $this->getScopeLabel($attribute)]
        );

        return $attributeContainer;
    }

    /**
     * Retrieve container child fields.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string $groupCode Attribute group code.
     * @param int $sortOrder Sort order.
     * @return array
     */
    public function getContainerChildren(AttributeInterface $attribute, string $groupCode, int $sortOrder): array
    {
        $child = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder);
        if (!$child) {
            return [];
        }

        return [$attribute->getAttributeCode() => $child];
    }

    /**
     * Retrieve attribute meta config.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string $groupCode Attribute group code.
     * @param int $sortOrder Sort order.
     * @return array
     */
    public function setupAttributeMeta(AttributeInterface $attribute, string $groupCode, int $sortOrder): array
    {
        $configPath = static::META_CONFIG_PATH;

        $meta = $this->arrayManager->set($configPath, [], [
            'dataType'    => $attribute->getFrontendInput(),
            'formElement' => $this->eavHelper->getFormElement($attribute->getFrontendInput()),
            'visible'     => true,
            'required'    => $attribute->getIsRequired(),
            'notice'      => $attribute->getNote(),
            'default'     => $attribute->getDefaultValue(),
            'label'       => $attribute->getDefaultFrontendLabel(),
            'code'        => $attribute->getAttributeCode(),
            'source'      => $groupCode,
            'scopeLabel'  => $this->getScopeLabel($attribute),
            'globalScope' => $this->isScopeGlobal($attribute),
            'sortOrder'   => $sortOrder * self::SORT_ORDER_MULTIPLIER,
        ]);

        $attributeModel = $this->getAttributeModel($attribute);
        if ($attributeModel->usesSource()) {
            $source = $attributeModel->getSource();
            $options = $source->getAllOptions();
            $meta = $this->arrayManager->merge(
                $configPath,
                $meta,
                ['options' => $this->convertOptionsValueToString($options)]
            );
        }

        /** @var AttributeInterface $attribute */
        if ($this->canDisplayUseDefault($attribute)) {
            $meta = $this->arrayManager->merge(
                $configPath,
                $meta,
                ['service' => ['template' => 'ui/form/element/helper/service']]
            );
        }

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['componentType' => Field::NAME]);
        }

        if (in_array($attribute->getAttributeCode(), $this->attributesToDisable)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['disabled' => true]);
        }

        $childData = $this->arrayManager->get($configPath, $meta, []);
        $rules = $this->validationRules->build($attribute, $childData);
        if ($rules) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['validation' => $rules]);
        }

        $meta = $this->addUseDefaultValueCheckbox($attribute, $meta);

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $meta = $this->customizeCheckbox($attribute, $meta);
                break;
            case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            case 'image':
                $meta = $this->customizeImage($attribute, $meta);
                break;
        }

        return $meta;
    }

    /**
     * Setup attribute data for the current entity.
     *
     * @param AttributeInterface $attribute Attribute.
     * @return mixed|NULL
     */
    public function setupAttributeData(AttributeInterface $attribute)
    {
        $value = null;
        $entity = $this->locator->getEntity();
        $entityId = $entity->getId();
        $prevSetId = $this->getPreviousSetId();

        /** @var array $haystack */
        $haystack = $this->getPreviousSetAttributes();
        $needle = $attribute->getAttributeCode();

        // @phpstan-ignore-next-line
        $notUsed = !$prevSetId || $prevSetId && !in_array($needle, $haystack);

        if ($entityId && $notUsed) {
            $value = $this->getValue($attribute);
        }

        return $value;
    }

    /**
     * Return current attribute set id
     *
     * @return int|string
     */
    private function getAttributeSetId()
    {
        return $this->locator->getEntity()->getAttributeSetId();
    }

    /**
     * List of attributes of the form.
     */
    private function getAttributes(): array
    {
        return $this->eavHelper->getAttributes($this->locator->getEntity(), $this->getAttributeSetId());
    }

    /**
     * Return previous entity attribute set id.
     */
    private function getPreviousSetId(): int
    {
        // @phpstan-ignore-next-line
        return (int) $this->locator->getEntity()->getPrevAttributeSetId();
    }

    /**
     * Return previous entity attributes.
     */
    private function getPreviousSetAttributes(): array
    {
        return $this->eavHelper->getAttributes($this->locator->getEntity(), $this->getPreviousSetId());
    }

    /**
     * List of attribute groups of the form.
     *
     * @return AttributeGroupInterface[]
     */
    private function getGroups(): array
    {
        return $this->eavHelper->getGroups($this->getAttributeSetId());
    }

    /**
     * Build attribute meta.
     *
     * @param array  $attributes Attributes.
     * @param string $groupCode  Current group code.
     * @return array
     */
    private function getAttributesMeta(array $attributes, string $groupCode): array
    {
        $meta = [];

        foreach ($attributes as $sortOrder => $attribute) {
            if (in_array($attribute->getFrontendInput(), $this->bannedInputTypes)) {
                continue;
            }

            if (in_array($attribute->getAttributeCode(), $this->attributesToEliminate)) {
                continue;
            }

            $attributeContainer = $this->setupAttributeContainerMeta($attribute);
            if (!$attributeContainer) {
                continue;
            }

            $attributeContainer = $this->addContainerChildren($attributeContainer, $attribute, $groupCode, $sortOrder);

            $meta[self::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $attributeContainer;
        }

        return $meta;
    }

    /**
     * Retrieve attribute scope label.
     *
     * @param AttributeInterface $attribute Attribute.
     * @return Phrase|string
     */
    private function getScopeLabel(AttributeInterface $attribute)
    {
        return $this->eavHelper->getScopeLabel($attribute);
    }

    /**
     * Check if attribute scope is global.
     *
     * @param AttributeInterface $attribute Attribute.
     */
    private function isScopeGlobal(AttributeInterface $attribute): bool
    {
        return $this->eavHelper->isScopeGlobal($attribute);
    }

    /**
     * Whether attribute can have default value.
     *
     * @param AttributeInterface $attribute Attribute.
     */
    private function canDisplayUseDefault(AttributeInterface $attribute): bool
    {
        return $this->eavHelper->canDisplayUseDefault($attribute, $this->locator->getEntity());
    }

    /**
     * Append a use default value if needed.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array $meta Attribute meta.
     * @return array
     */
    private function addUseDefaultValueCheckbox(AttributeInterface $attribute, array $meta): array
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);

        if ($canDisplayService) {
            $storeId = $this->locator->getStore()->getId();
            $entity  = $this->locator->getEntity();
            $meta['arguments']['data']['config']['service'] = ['template' => 'ui/form/element/helper/service'];
            $meta['arguments']['data']['config']['disabled'] =
                !$this->eavHelper->hasValueForStore($entity, $attribute, (int) $storeId);
        }

        return $meta;
    }

    /**
     * Return current value for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     * @return mixed|NULL
     */
    private function getValue(AttributeInterface $attribute)
    {
        /** @var DataObject $entity */
        $entity = $this->locator->getEntity();
        /** @var string $data */
        $data = $attribute->getAttributeCode();
        return $entity->getData($data);
    }

    /**
     * Resolve data persistence
     *
     * @param array $data Data.
     * @return array
     */
    private function resolvePersistentData(array $data): array
    {
        $persistentData = (array) $this->dataPersistor->get('entity');
        $this->dataPersistor->clear('entity');
        $entityId = $this->locator->getEntity()->getId();

        if (empty($data[$entityId][self::DATA_SOURCE_DEFAULT])) {
            $data[$entityId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$entityId] = array_replace_recursive($data[$entityId][self::DATA_SOURCE_DEFAULT], $persistentData);

        return $data;
    }

    /**
     * Add wysiwyg properties.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array $meta Meta data of data provider.
     * @return array
     */
    private function customizeWysiwyg(AttributeInterface $attribute, array $meta): array
    {
        /** @var EavAttribute $attribute */
        if (!$attribute->getIsWysiwygEnabled()) {
            return $meta;
        }

        $meta['arguments']['data']['config']['formElement'] = WysiwygElement::NAME;
        $meta['arguments']['data']['config']['wysiwyg'] = true;
        $meta['arguments']['data']['config']['wysiwygConfigData'] = [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];

        return $meta;
    }

    /**
     * Customize checkboxes.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array $meta Metadata of data provider.
     * @return array
     */
    private function customizeCheckbox(AttributeInterface $attribute, array $meta): array
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta['arguments']['data']['config']['prefer'] = 'toggle';
            $meta['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }

        return $meta;
    }

    /**
     * Customize image field.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array $meta current metadata of data provider.
     * @return array
     */
    private function customizeImage(AttributeInterface $attribute, array $meta): array
    {
        if ($attribute->getFrontendInput() !== 'image') {
            return $meta;
        }
        $meta['arguments']['data']['config']['formElement'] = 'fileUploader';
        $meta['arguments']['data']['config']['allowedExtensions'] = 'jpg jpeg gif png';
        $meta['arguments']['data']['config']['elementTmpl'] = 'ui/form/element/uploader/image';
        $meta['arguments']['data']['config']['uploaderConfig'] = [
            'url' => 'scoped_eav/entity/image_upload',
        ];

        return $meta;
    }

    /**
     * Return image data.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string $value Attribute value.
     * @return string|array
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function overrideImageUploaderData(AttributeInterface $attribute, string $value)
    {
        if ($attribute->getFrontendInput() !== 'image') {
            return $value;
        }

        $return = [];
        if ($this->fileInfo->isExist($value)) {
            $stat = $this->fileInfo->getStat($value);
            $mime = $this->fileInfo->getMimeType($value);

            /** @var AbstractEntity $entity */
            $entity = $this->locator->getEntity();
            $viewUrl = $entity->getImageUrl($attribute->getAttributeCode());

            if (empty($viewUrl)) {
                $viewUrl = '';
            }

            $return[] = [
                'file' => $value,
                'size' => $stat ? $stat['size'] : 0,
                'url' => $viewUrl,
                'name' => basename($value), // @codingStandardsIgnoreLine (MEQP1.Security.DiscouragedFunction.Found)
                'type' => $mime,
            ];
        }

        return $return;
    }

    /**
     * Convert options value to string.
     *
     * @param array $options
     * @return array
     */
    private function convertOptionsValueToString(array $options): array
    {
        array_walk(
            $options,
            function (&$value): void {
                if (isset($value['value']) && is_scalar($value['value'])) {
                    $value['value'] = (string) $value['value'];
                }
            }
        );

        return $options;
    }

    /**
     * Convert options value to string.
     *
     * @param mixed $attribute
     */
    private function getAttributeModel($attribute): EavAttribute
    {
        if ($attribute instanceof EavAttribute) {
            return $attribute;
        }
        $attributeId = $attribute->getAttributeId();

        if (!array_key_exists($attributeId, $this->attributesCache)) {
            $this->attributesCache[$attributeId] = $this->eavAttributeFactory->create()->load($attributeId);
        }

        return $this->attributesCache[$attributeId];
    }
}
