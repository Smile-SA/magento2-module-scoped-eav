<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Entity edit delete button.
 */
class Delete extends Generic
{
    private Escaper $jsEscape;

    /**
     * @param Context $context  Context.
     * @param Registry $registry Registry.
     * @param Escaper $jsEscape JS Escape.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Escaper $jsEscape
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
     */
    private function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getEntity()->getId()]);
    }
}
