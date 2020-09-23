<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Smile\ScopedEav\Api\Data\EntityInterface;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Zend\Validator\Regex;
use Zend\Validator\RegexFactory;

/**
 * Scoped EAV helper.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Product\UrlFactory
     */
    private $urlFactory;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;

    /**
     * @var \Magento\Ui\DataProvider\Mapper\FormElement
     */
    private $formElementMapper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var RegexFactory
     */
    private $regexFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context         $context           Context.
     * @param \Magento\Catalog\Model\Product\UrlFactory     $urlFactory        Url factory.
     * @param \Magento\Catalog\Helper\Product               $productHelper     Product helper.
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager      Store manager.
     * @param \Magento\Ui\DataProvider\Mapper\FormElement   $formElementMapper Form element mapper.
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool      Entity manager metadata pool.
     * @param RegexFactory                                  $regexFactory      Regexp validator factory.
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\UrlFactory $urlFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Ui\DataProvider\Mapper\FormElement $formElementMapper,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
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
    public function generateAttributeCodeFromLabel($label)
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
    public function getAttributeBackendModelByInputType($inputType)
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
    public function getAttributeSourceModelByInputType($inputType)
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
    public function getFormElement($frontendInput)
    {
        $valueMap = $this->formElementMapper->getMappings();

        return isset($valueMap[$frontendInput]) ? $valueMap[$frontendInput] : $frontendInput;
    }

    /**
     * Scope label for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return string|\Magento\Framework\Phrase
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
    public function isScopeGlobal(AttributeInterface $attribute)
    {
        return $attribute->getScope() === AttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Returns entity manager metadata for an entity.
     *
     * @param EntityInterface $entity Entity.
     *
     * @return \Magento\Framework\EntityManager\EntityMetadataInterface
     */
    public function getEntityMetadata(EntityInterface $entity)
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
    public function getEntityInterface(EntityInterface $entity)
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
