<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

use Magento\Catalog\Model\AbstractModel;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Entity edit delete button.
 */
class Delete extends Generic
{
    // @phpstan-ignore-next-line
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
     * @inheritDoc
     */
    public function getButtonData()
    {
        /** @var AbstractModel $this */
        if ($this->getEntity()->isReadonly() || !$this->getEntity()->getId()) {
            return [];
        }

        // @phpstan-ignore-next-line
        $deleteMessage = $this->jsEscape->escapeJsQuote(
            (string) __("Are you sure you want to delete the entity ?")
        );

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
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getEntity()->getId()]);
    }
}
