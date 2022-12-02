<?php

declare(strict_types=1);

namespace Smile\ScopedEav\ViewModel;

use Laminas\Validator\Regex;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\UrlFactory;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Mapper\FormElement;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV view model.
 */
class Data implements ArgumentInterface
{
    private UrlFactory $urlFactory;

    private Product $productHelper;

    private FormElement $formElementMapper;

    private StoreManagerInterface $storeManager;

    private MetadataPool $metadataPool;

    private Registry $coreRegistry;

    /**
     * Constructor.
     *
     * @param UrlFactory $urlFactory Url factory.
     * @param Product $productHelper Product helper.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param FormElement $formElementMapper Form element mapper.
     * @param MetadataPool $metadataPool Entity manager metadata pool.
     * @param Registry $coreRegistry Regexp validator factory.
     */
    public function __construct(
        UrlFactory $urlFactory,
        Product $productHelper,
        StoreManagerInterface $storeManager,
        FormElement $formElementMapper,
        MetadataPool $metadataPool,
        Registry $coreRegistry
    ) {
        $this->storeManager = $storeManager;
        $this->urlFactory = $urlFactory;
        $this->productHelper = $productHelper;
        $this->formElementMapper = $formElementMapper;
        $this->metadataPool = $metadataPool;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Generate attribute code from label.
     *
     * @param string $label Attribute label.
     */
    public function generateAttributeCodeFromLabel(string $label): string
    {
        $code = substr(preg_replace('/[^a-z_0-9]/', '_', $this->urlFactory->create()->formatUrlKey($label)), 0, 30);

        /** @var Regex $validatorAttrCode */
        $validatorAttrCode = new Regex('/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/');

        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5((string) time()), 0, 8)); // @codingStandardsIgnoreLine
        }

        return $code;
    }

    /**
     * Infers attribute backend model from input type.
     *
     * @param string $inputType Input type.
     * @return string|NULL
     */
    public function getAttributeBackendModelByInputType(string $inputType): ?string
    {
        if ($inputType == 'image') {
            return \Smile\ScopedEav\Model\Entity\Attribute\Backend\Image::class;
        }
        return $this->productHelper->getAttributeBackendModelByInputType($inputType);
    }

    /**
     * Infers attribute source model from input type.
     *
     * @param string $inputType Input type.
     * @return string|NULL
     */
    public function getAttributeSourceModelByInputType(string $inputType): ?string
    {
        return $this->productHelper->getAttributeSourceModelByInputType($inputType);
    }

    /**
     * Return form element by frontend input.
     *
     * @param string $frontendInput Frontend input.
     * @return string|NULL
     */
    public function getFormElement(string $frontendInput): ?string
    {
        $valueMap = $this->formElementMapper->getMappings();

        return $valueMap[$frontendInput] ?? $frontendInput;
    }

    /**
     * Scope label for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     * @return string|Phrase
     */
    public function getScopeLabel(AttributeInterface $attribute)
    {
        if ($this->storeManager->isSingleStoreMode() || $attribute->getFrontendInput() === $attribute::FRONTEND_INPUT) {
            return '';
        }

        switch ($attribute->getScope()) {
            case AttributeInterface::SCOPE_GLOBAL_TEXT:
                return __('[GLOBAL]');
            case AttributeInterface::SCOPE_WEBSITE_TEXT:
                return __('[WEBSITE]');
            case AttributeInterface::SCOPE_STORE_TEXT:
                return __('[STORE VIEW]');
        }

        return '';
    }

    /**
     * Check if attribute is global.
     *
     * @param AttributeInterface $attribute Attribute.
     */
    public function isScopeGlobal(AttributeInterface $attribute): bool
    {
        return $attribute->getScope() === AttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Returns entity manager metadata for an entity.
     *
     * @param EntityInterface $entity Entity.
     */
    public function getEntityMetadata(EntityInterface $entity): EntityMetadataInterface
    {
        $interface = $this->getEntityInterface($entity);

        return $this->metadataPool->getMetadata($interface);
    }

    /**
     * Returns the interface implemented by an entity.
     *
     * @param EntityInterface $entity Entity.
     * @return string|NULL
     */
    public function getEntityInterface(EntityInterface $entity): ?string
    {
        $interface = null;

        foreach (class_implements(get_class($entity)) as $currentInterface) {
            if (
                in_array(EntityInterface::class, class_implements($currentInterface))
                && $currentInterface !== EntityInterface::class
            ) {
                $interface = $currentInterface;
            }
        }

        return $interface;
    }

    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields(): array
    {
        if ($this->coreRegistry->registry('attribute_type_hidden_fields')) {
            return $this->coreRegistry->registry('attribute_type_hidden_fields');
        } else {
            return [];
        }
    }
}
