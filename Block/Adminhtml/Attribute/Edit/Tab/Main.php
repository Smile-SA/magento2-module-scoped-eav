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

namespace Smile\ScopedEav\Block\Adminhtml\Attribute\Edit\Tab;

/**
 * Scoped entity attribute general properties form.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Main extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
{
    /**
     * @var string[]
     */
    private $disableScopeChangeList;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                            $context                Context.
     * @param \Magento\Framework\Registry                                        $registry               Registry.
     * @param \Magento\Framework\Data\FormFactory                                $formFactory            Form factory
     * @param \Magento\Eav\Helper\Data                                           $eavData                EAV helper.
     * @param \Magento\Config\Model\Config\Source\YesnoFactory                   $yesnoFactory           Yes/No source factory.
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory       Form input type factory.
     * @param \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker              $propertyLocker         Form property locker.
     * @param array                                                              $disableScopeChangeList List of attribute scope can
     *                                                                                                   not be changed.
     * @param array                                                              $data                   Additional data.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        array $disableScopeChangeList = [],
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $eavData, $yesnoFactory, $inputTypeFactory, $propertyLocker, $data);
        $this->disableScopeChangeList = $disableScopeChangeList;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $attributeObject = $this->getAttributeObject();

        $form     = $this->getForm();
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
    private function getAttributeScopes()
    {
        $scopes = [
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE => __('Store View'),
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE => __('Website'),
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL => __('Global'),
        ];

        return $scopes;
    }
}
