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

/**
 * Scoped EAV helper.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
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
     * Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context     $context       Context.
     * @param \Magento\Catalog\Model\Product\UrlFactory $urlFactory    Url factory.
     * @param \Magento\Catalog\Helper\Product           $productHelper Product helper.
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\UrlFactory $urlFactory,
        \Magento\Catalog\Helper\Product $productHelper
    ) {
        parent::__construct($context);
        $this->urlFactory    = $urlFactory;
        $this->productHelper = $productHelper;
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

        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);

        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
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
}
