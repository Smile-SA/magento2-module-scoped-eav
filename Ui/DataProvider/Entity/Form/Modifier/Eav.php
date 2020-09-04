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
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Container;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use Magento\Catalog\Model\Category\FileInfo;

/**
 * Scoped EAV attribute form modifier.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Eav extends AbstractModifier
{
    /**
     * @var Helper\Eav
     */
    private $eavHelper;

    /**
     * @var \Smile\ScopedEav\Model\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    private $arrayManager;

    /**
     * @var \Smile\ScopedEav\Ui\DataProvider\Entity\Form\EavValidationRules
     */
    private $validationRules;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $bannedInputTypes;

    /**
     * @var array
     */
    private $attributesToEliminate;

    /**
     * @var array
     */
    private $attributesToDisable;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * Constructor.
     *
     * @param Helper\Eav                                                      $eavHelper             EAV helper.
     * @param \Smile\ScopedEav\Model\Locator\LocatorInterface                 $locator               Entity locator.
     * @param \Magento\Framework\Stdlib\ArrayManager                          $arrayManager          Array manager.
     * @param \Smile\ScopedEav\Ui\DataProvider\Entity\Form\EavValidationRules $validationRules       EAV validation rules
     * @param \Magento\Framework\App\Request\DataPersistorInterface           $dataPersistor         Data persistor.
     * @param FileInfo                                                        $fileInfo              File information.
     * @param array                                                           $bannedInputTypes      Input types removed from the form.
     * @param array                                                           $attributesToEliminate Attribute codes removed from the form.
     * @param array                                                           $attributesToDisable   Attribute codes to be disabled.
     */
    public function __construct(
        Helper\Eav $eavHelper,
        \Smile\ScopedEav\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Smile\ScopedEav\Ui\DataProvider\Entity\Form\EavValidationRules $validationRules,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        FileInfo $fileInfo,
        array $bannedInputTypes = [],
        array $attributesToEliminate = [],
        array $attributesToDisable = []
    ) {
        $this->eavHelper             = $eavHelper;
        $this->locator               = $locator;
        $this->arrayManager          = $arrayManager;
        $this->bannedInputTypes      = $bannedInputTypes;
        $this->validationRules       = $validationRules;
        $this->dataPersistor         = $dataPersistor;
        $this->attributesToEliminate = $attributesToEliminate;
        $this->attributesToDisable   = $attributesToDisable;
        $this->fileInfo = $fileInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyData(array $data)
    {
        if (!$this->locator->getEntity()->getId() && $this->dataPersistor->get('entity')) {
            return $this->resolvePersistentData($data);
        }

        $entityId = $this->locator->getEntity()->getId();

        foreach (array_keys($this->getGroups()) as $groupCode) {
            $attributes = ! empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            foreach ($attributes as $attribute) {
                if (null !== ($attributeValue = $this->setupAttributeData($attribute))) {
                    $attributeValue = $this->overrideImageUploaderData($attribute, $attributeValue);
                    $data[$entityId][self::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $attributeValue;
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = 0;

        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['label'] = __('%1', $group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = false;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_ENTITY;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] = $sortOrder * self::SORT_ORDER_MULTIPLIER;
            }

            $sortOrder ++;
        }

        return $meta;
    }

    /**
     * Build attribute container.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return array
     */
    public function setupAttributeContainerMeta(AttributeInterface $attribute)
    {
        $containerMeta = $this->arrayManager->set(
            self::META_CONFIG_PATH,
            [],
            [
                'formElement'   => Container::NAME,
                'componentType' => Container::NAME,
                'breakLine'     => false,
                'label'         => $attribute->getDefaultFrontendLabel(),
                'required'      => $attribute->getIsRequired(),
            ]
        );

        if ($attribute->getIsWysiwygEnabled()) {
            $containerMeta = $this->arrayManager->merge(
                self::META_CONFIG_PATH,
                $containerMeta,
                ['component' => 'Magento_Ui/js/form/components/group']
            );
        }

        return $containerMeta;
    }

    /**
     * Add attribute container children.
     *
     * @param array              $attributeContainer Attribute container.
     * @param AttributeInterface $attribute          Attibute.
     * @param string             $groupCode          Group code.
     * @param int                $sortOrder          Attribute sort order.
     *
     * @return array
     */
    public function addContainerChildren(array $attributeContainer, AttributeInterface $attribute, $groupCode, $sortOrder)
    {
        foreach ($this->getContainerChildren($attribute, $groupCode, $sortOrder) as $childCode => $child) {
            $attributeContainer['children'][$childCode] = $child;
        }

        $attributeContainer = $this->arrayManager->merge(
            self::META_CONFIG_PATH,
            $attributeContainer,
            ['sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER, 'scopeLabel' => $this->getScopeLabel($attribute)]
        );

        return $attributeContainer;
    }

    /**
     * Retrieve container child fields.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string             $groupCode Attribute group code.
     * @param int                $sortOrder Sort order.
     *
     * @return array
     */
    public function getContainerChildren(AttributeInterface $attribute, $groupCode, $sortOrder)
    {
        if (!($child = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder))) {
            return [];
        }

        return [$attribute->getAttributeCode() => $child];
    }

    /**
     * Retrieve attribute meta config.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string             $groupCode Attribute group code.
     * @param int                $sortOrder Sort order.
     *
     * @return array
     */
    public function setupAttributeMeta(AttributeInterface $attribute, $groupCode, $sortOrder)
    {
        $configPath = static::META_CONFIG_PATH;

        $meta = $this->arrayManager->set($configPath, [], [
            'dataType'    => $attribute->getFrontendInput(),
            'formElement' => $this->eavHelper->getFormElement($attribute->getFrontendInput()),
            'visible'     => true,
            'required'    => $attribute->getIsRequired(),
            'notice'      => $attribute->getNote(),
            'default'     => $attribute->getDefaultValue(),
            'label'       => $attribute->getDefaultFrontendLabel(),
            'code'        => $attribute->getAttributeCode(),
            'source'      => $groupCode,
            'scopeLabel'  => $this->getScopeLabel($attribute),
            'globalScope' => $this->isScopeGlobal($attribute),
            'sortOrder'   => $sortOrder * self::SORT_ORDER_MULTIPLIER,
        ]);

        if ($attribute->usesSource()) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['options' => $attribute->getSource()->getAllOptions()]);
        }

        if ($this->canDisplayUseDefault($attribute)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['service' => ['template' => 'ui/form/element/helper/service']]);
        }

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['componentType' => Field::NAME]);
        }

        if (in_array($attribute->getAttributeCode(), $this->attributesToDisable)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['disabled' => true]);
        }

        $childData = $this->arrayManager->get($configPath, $meta, []);
        if (($rules = $this->validationRules->build($attribute, $childData))) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['validation' => $rules]);
        }

        $meta = $this->addUseDefaultValueCheckbox($attribute, $meta);

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $meta = $this->customizeCheckbox($attribute, $meta);
                break;
            case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            case 'image':
                $meta = $this->customizeImage($attribute, $meta);
                break;
        }

        return $meta;
    }

    /**
     * Setup attribute data for the current entity.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return mixed|NULL
     */
    public function setupAttributeData(AttributeInterface $attribute)
    {
        $value    = null;
        $entity   = $this->locator->getEntity();
        $entityId = $entity->getId();
        $prevSetId = $this->getPreviousSetId();

        $notUsed = ! $prevSetId || ($prevSetId && ! in_array($attribute->getAttributeCode(), $this->getPreviousSetAttributes()));

        if ($entityId && $notUsed) {
            $value = $this->getValue($attribute);
        }

        return $value;
    }

    /**
     * Return current attribute set id
     *
     * @return int|null
     */
    private function getAttributeSetId()
    {
        return $this->locator->getEntity()->getAttributeSetId();
    }

    /**
     * List of attributes of the form.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterface[]
     */
    private function getAttributes()
    {
        return $this->eavHelper->getAttributes($this->locator->getEntity(), $this->getAttributeSetId());
    }

    /**
     * Return previous entity attribute set id.
     *
     * @return int
     */
    private function getPreviousSetId()
    {
        return (int) $this->locator->getEntity()->getPrevAttributeSetId();
    }

    /**
     * Return previous entity attributes.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterface[]
     */
    private function getPreviousSetAttributes()
    {
        return $this->eavHelper->getAttributes($this->locator->getEntity(), $this->getPreviousSetId());
    }

    /**
     * List of attribute groups of the form.
     *
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface[]
     */
    private function getGroups()
    {
        return $this->eavHelper->getGroups($this->getAttributeSetId());
    }

    /**
     * Build attribute meta.
     *
     * @param array  $attributes Attributes.
     * @param string $groupCode  Current group code.
     *
     * @return array
     */
    private function getAttributesMeta(array $attributes, $groupCode)
    {
        $meta = [];

        foreach ($attributes as $sortOrder => $attribute) {
            if (in_array($attribute->getFrontendInput(), $this->bannedInputTypes)) {
                continue;
            }

            if (in_array($attribute->getAttributeCode(), $this->attributesToEliminate)) {
                continue;
            }

            if (!($attributeContainer = $this->setupAttributeContainerMeta($attribute))) {
                continue;
            }

            $attributeContainer = $this->addContainerChildren($attributeContainer, $attribute, $groupCode, $sortOrder);

            $meta[self::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $attributeContainer;
        }

        return $meta;
    }

    /**
     * Retrieve attribute scope label.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getScopeLabel(AttributeInterface $attribute)
    {
        return $this->eavHelper->getScopeLabel($attribute);
    }

    /**
     * Check if attribute scope is global.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return bool
     */
    private function isScopeGlobal(AttributeInterface $attribute)
    {
        return $this->eavHelper->isScopeGlobal($attribute);
    }

    /**
     * Whether attribute can have default value.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return bool
     */
    private function canDisplayUseDefault(AttributeInterface $attribute)
    {
        return $this->eavHelper->canDisplayUseDefault($attribute, $this->locator->getEntity());
    }

    /**
     * Append a use default value if needed.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array              $meta      Attribute meta.
     *
     * @return array
     */
    private function addUseDefaultValueCheckbox(AttributeInterface $attribute, array $meta)
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);

        if ($canDisplayService) {
            $storeId = $this->locator->getStore()->getId();
            $entity  = $this->locator->getEntity();
            $meta['arguments']['data']['config']['service'] = ['template' => 'ui/form/element/helper/service'];
            $meta['arguments']['data']['config']['disabled'] = !$this->eavHelper->hasValueForStore($entity, $attribute, $storeId);
        }

        return $meta;
    }

    /**
     * Return current value for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return mixed|NULL
     */
    private function getValue(AttributeInterface $attribute)
    {
        $entity = $this->locator->getEntity();

        return $entity->getData($attribute->getAttributeCode());
    }

    /**
     * Resolve data persistence
     *
     * @param array $data Data.
     *
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array) $this->dataPersistor->get('entity');
        $this->dataPersistor->clear('entity');
        $entityId = $this->locator->getEntity()->getId();

        if (empty($data[$entityId][self::DATA_SOURCE_DEFAULT])) {
            $data[$entityId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$entityId] = array_replace_recursive($data[$entityId][self::DATA_SOURCE_DEFAULT], $persistentData);

        return $data;
    }

    /**
     * Add wysiwyg properties.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array              $meta      Meta data of data provider.
     *
     * @return array
     */
    private function customizeWysiwyg(AttributeInterface $attribute, array $meta)
    {
        if (!$attribute->getIsWysiwygEnabled()) {
            return $meta;
        }

        $meta['arguments']['data']['config']['formElement'] = WysiwygElement::NAME;
        $meta['arguments']['data']['config']['wysiwyg'] = true;
        $meta['arguments']['data']['config']['wysiwygConfigData'] = [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];

        return $meta;
    }

    /**
     * Customize checkboxes.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array              $meta      Metadata of data provider.
     *
     * @return array
     */
    private function customizeCheckbox(AttributeInterface $attribute, array $meta)
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta['arguments']['data']['config']['prefer'] = 'toggle';
            $meta['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }

        return $meta;
    }

    /**
     * Customize image field.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array              $meta      Current metadata of data provider.
     *
     * @return array
     */
    private function customizeImage(AttributeInterface $attribute, array $meta)
    {
        if ($attribute->getFrontendInput() !== 'image') {
            return $meta;
        }
        $meta['arguments']['data']['config']['formElement'] = 'fileUploader';
        $meta['arguments']['data']['config']['allowedExtensions'] = 'jpg jpeg gif png';
        $meta['arguments']['data']['config']['elementTmpl'] = 'ui/form/element/uploader/image';
        $meta['arguments']['data']['config']['uploaderConfig'] = [
            'url' => 'scoped_eav/entity/image_upload',
        ];

        return $meta;
    }

    /**
     * Return image data.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param string             $value     Attribute value.
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function overrideImageUploaderData(AttributeInterface $attribute, $value)
    {
        if ($attribute->getFrontendInput() !== 'image') {
            return $value;
        }

        $return = [];
        if ($this->fileInfo->isExist($value)) {
            $stat = $this->fileInfo->getStat($value);
            $mime = $this->fileInfo->getMimeType($value);

            $viewUrl = $this->locator->getEntity()->getImageUrl($attribute->getAttributeCode());

            $return[] = [
                'file' => $value,
                'size' => isset($stat) ? $stat['size'] : 0,
                'url' => isset($viewUrl) ? $viewUrl : '',
                'name' => basename($value), // @codingStandardsIgnoreLine (MEQP1.Security.DiscouragedFunction.Found)
                'type' => $mime,
            ];
        }

        return $return;
    }
}
