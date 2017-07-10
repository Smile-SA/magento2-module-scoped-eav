<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Block\Adminhtml\Set\Toolbar;

/**
 * Attribute set listing main container.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Main extends \Magento\Backend\Block\Template
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'addButton',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add Attribute Set'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/add') . '\')',
                'class' => 'add primary add-set',
            ]
        );

        return parent::_prepareLayout();
    }
}
