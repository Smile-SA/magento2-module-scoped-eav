<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml;

use Magento\Backend\Block\Widget\Button\SplitButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SortOrder;

/**
 * Scoped EAV entity listing main container.
 */
abstract class AbstractEntity extends Container
{
    private Config $eavConfig;

    /**
     * Constructor.
     *
     * @param Context $context Context
     * @param Config $eavConfig EAV config.
     * @param array $data Additional data.
     */
    public function __construct(
        Context $context,
        Config $eavConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->eavConfig = $eavConfig;
    }

    /**
     * Check whether it is single store mode
     */
    public function isSingleStoreMode(): bool
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_entity',
            'label' => __('Add'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => SplitButton::class,
            'options' => $this->getAddEntityButtonOptions(),
        ];

        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function getAddEntityButtonOptions(): array
    {
        $entityType    = $this->eavConfig->getEntityType($this->getEntityTypeCode());
        $attributeSets = $entityType->getAttributeSetCollection()->addOrder("attribute_set_name", SortOrder::SORT_ASC);
        $defaultSetId  = $entityType->getDefaultAttributeSetId();

        $splitButtonOptions = [];

        foreach ($attributeSets as $attributeSet) {
            $splitButtonOptions[$attributeSet->getId()] = [
                'label' => __($attributeSet->getAttributeSetName()),
                'onclick' => "setLocation('" . $this->getEntityCreateUrl($attributeSet->getId()) . "')",
                'default' => $defaultSetId,
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve entity create url by specified attribute set id.
     *
     * @param string|int $attributeSetId Attribute set id.
     */
    protected function getEntityCreateUrl($attributeSetId): string
    {
        return $this->getUrl('*/*/new', ['set' => $attributeSetId]);
    }

    /**
     * Get current entity type code.
     */
    abstract protected function getEntityTypeCode(): string;
}
