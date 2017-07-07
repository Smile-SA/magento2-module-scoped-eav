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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Scoped EAV entity attribute deletion controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Delete extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $attribute = $this->getAttribute();

            if (!$attribute || !$attribute->getId()) {
                throw new NoSuchEntityException(__('Attribute does not exists'));
            }

            $attribute->delete();

            $this->messageManager->addSuccessMessage(__('Attribute has been deleted'));
            $response = $this->_redirect("*/*/index");
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }
}
