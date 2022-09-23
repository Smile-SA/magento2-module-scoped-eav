<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\Component\Control\Container;

/**
 * Entity edit save button.
 */
class Save extends Generic
{
    private string $formName;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param Registry $registry Registry.
     * @param string $formName Name of the form.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        string $formName
    ) {
        parent::__construct($context, $registry);

        $this->formName = $formName;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if ($this->getEntity()->isReadonly()) {
            return [];
        }

        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            ['targetName' => $this->formName, 'actionName' => 'save', 'params' => [false]],
                        ],
                    ],
                ],
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
            'sort_order' => 30,
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            ['targetName' => $this->formName, 'actionName' => 'save', 'params' => [true, ['back' => 'new']]],
                        ],
                    ],
                ],
            ],
        ];

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            ['targetName' => $this->formName, 'actionName' => 'save', 'params' => [true]],
                        ],
                    ],
                ],
            ],
        ];

        return $options;
    }
}
