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

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

/**
 * Entity edit delete button.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Delete extends Generic
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $jsEscape;

    /**
     *
     * @param \Magento\Framework\View\Element\UiComponent\Context $context  Context.
     * @param \Magento\Framework\Registry                         $registry Registry.
     * @param \Magento\Framework\Escaper                          $jsEscape JS Escape.
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $jsEscape
    ) {
        parent::__construct($context, $registry);
        $this->jsEscape = $jsEscape;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if ($this->getEntity()->isReadonly() || !$this->getEntity()->getId()) {
            return [];
        }

        $deleteMessage = $this->jsEscape->escapeJsQuote(__("Are you sure you want to delete the entity ?"));

        return [
            'label'      => __('Delete'),
            'on_click'   => 'deleteConfirm(\'' . $deleteMessage . '\', \'' . $this->getDeleteUrl() . '\')',
            'class'      => 'delete',
            'sort_order' => 20,
        ];
    }

    /**
     * Get delete URL.
     *
     * @return string
     */
    private function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getEntity()->getId()]);
    }
}
