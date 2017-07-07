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

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped EAV entity attribute edit controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Edit extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $title     = __('New attribute');
            $attribute = $this->getAttribute();
            if ($attribute->getAttributeId()) {
                $title = __('Edit %1', $attribute->getAttributeCode());
            }
            $response = $this->createActionPage($title);
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }
}
