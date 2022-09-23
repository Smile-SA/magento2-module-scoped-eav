<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Helper\Data;
use Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Data\Form\Element\Select;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Scoped entity attribute general properties form.
 */
class Main extends AbstractMain
{
    /**
     * @var string[]
     */
    private array $disableScopeChangeList;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param Registry $registry Registry.
     * @param FormFactory $formFactory Form factory
     * @param Data $eavData EAV helper.
     * @param YesnoFactory $yesnoFactory Yes/No source factory.
     * @param InputtypeFactory $inputTypeFactory Form input type factory.
     * @param PropertyLocker $propertyLocker Form property locker.
     * @param array $disableScopeChangeList List of attribute scope can not be changed.
     * @param array $data Additional data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $eavData,
        YesnoFactory $yesnoFactory,
        InputtypeFactory $inputTypeFactory,
        PropertyLocker $propertyLocker,
        array $disableScopeChangeList = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
        $this->disableScopeChangeList = $disableScopeChangeList;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm(): self
    {
        parent::_prepareForm();

        $attributeObject = $this->getAttributeObject();

        $form     = $this->getForm();

        // Change Frontend input field name.
        /** @var Select $element */
        $element = $form->getElement('frontend_input');
        $element->setLabel(__('Input Type'));

        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField(
            'is_global',
            'select',
            [
                'name'   => 'is_global',
                'label'  => __('Scope'),
                'title'  => __('Scope'),
                'note'   => __('Declare attribute value saving scope.'),
                'value'  => $attributeObject->getIsGlobal(),
                'values' => $this->getAttributeScopes(),
            ],
            'attribute_code'
        );

        $form->getElement("attribute_code")->setRequired(false);

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        if (in_array($attributeObject->getAttributeCode(), $this->disableScopeChangeList)) {
            $form->getElement('is_global')->setDisabled(1);
        }

        return $this;
    }

    /**
     * List of scopes available for attribute.
     *
     * @return \Magento\Framework\Phrase[]
     */
    private function getAttributeScopes(): array
    {
        $scopes = [
            ScopedAttributeInterface::SCOPE_STORE => __('Store View'),
            ScopedAttributeInterface::SCOPE_WEBSITE => __('Website'),
            ScopedAttributeInterface::SCOPE_GLOBAL => __('Global'),
        ];

        return $scopes;
    }
}
