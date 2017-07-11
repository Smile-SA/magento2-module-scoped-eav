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

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set admin delete controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Delete extends \Smile\ScopedEav\Controller\Adminhtml\AbstractSet
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->getAttributeSet()->delete();
            $this->messageManager->addSuccessMessage(__('The attribute set has been removed.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete this set right now.'));
        }

        return $this->_redirect('*/*/index');
    }
}
