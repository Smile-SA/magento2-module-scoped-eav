<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Helper;

use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\UrlFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Mapper\FormElement;
use Smile\ScopedEav\Api\Data\EntityInterface;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Zend\Validator\Regex;
use Zend\Validator\RegexFactory;

/**
 * Scoped EAV helper.
 */
class Data extends AbstractHelper
{
    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * @var FormElement
     */
    private $formElementMapper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var RegexFactory
     */
    private $regexFactory;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param UrlFactory $urlFactory Url factory.
     * @param Product $productHelper Product helper.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param FormElement $formElementMapper Form element mapper.
     * @param MetadataPool $metadataPool Entity manager metadata pool.
     * @param RegexFactory $regexFactory Regexp validator factory.
     */
    public function __construct(
        Context $context,
        UrlFactory $urlFactory,
        Product $productHelper,
        StoreManagerInterface $storeManager,
        FormElement $formElementMapper,
        MetadataPool $metadataPool,
        RegexFactory $regexFactory
    ) {
        parent::__construct($context);

        $this->storeManager         = $storeManager;
        $this->urlFactory           = $urlFactory;
        $this->productHelper        = $productHelper;
        $this->formElementMapper    = $formElementMapper;
        $this->metadataPool         = $metadataPool;
        $this->regexFactory         = $regexFactory;
    }

    /**
     * Generate attribute code from label.
     *
     * @param string $label Attribute label.
     *
     * @return string
     */
    public function generateAttributeCodeFromLabel(string $label): string
    {
        $code = substr(preg_replace('/[^a-z_0-9]/', '_', $this->urlFactory->create()->formatUrlKey($label)), 0, 30);

        /** @var Regex $validatorAttrCode */
        $validatorAttrCode = $this->regexFactory->create(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);

        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8)); // @codingStandardsIgnoreLine
        }

        return $code;
    }

    /**
     * Infers attribute backend model from input type.
     *
     * @param string $inputType Input type.
     *
     * @return string|NULL
     */
    public function getAttributeBackendModelByInputType(string $inputType): ?string
    {
        if ($inputType == 'image') return 'Smile\ScopedEav\Model\Entity\Attribute\Backend\Image';
        return $this->productHelper->getAttributeBackendModelByInputType($inputType);
    }

    /**
     * Infers attribute source model from input type.
     *
     * @param string $inputType Input type.
     *
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
     *
     * @return string|NULL
     */
    public function getFormElement(string $frontendInput): ?string
    {
        $valueMap = $this->formElementMapper->getMappings();

        return isset($valueMap[$frontendInput]) ? $valueMap[$frontendInput] : $frontendInput;
    }

    /**
     * Scope label for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     *
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
     *
     * @return boolean
     */
    public function isScopeGlobal(AttributeInterface $attribute): bool
    {
        return $attribute->getScope() === AttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Returns entity manager metadata for an entity.
     *
     * @param EntityInterface $entity Entity.
     *
     * @return EntityMetadataInterface
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
     *
     * @return NULL|string
     */
    public function getEntityInterface(EntityInterface $entity): ?string
    {
        $interface = null;

        foreach (class_implements(get_class($entity)) as $currentInterface) {
            if (in_array(EntityInterface::class, class_implements($currentInterface)) && $currentInterface !== EntityInterface::class) {
                $interface = $currentInterface;
            }
        }

        return $interface;
    }
}
