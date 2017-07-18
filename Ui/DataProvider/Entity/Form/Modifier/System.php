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
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

/**
 * Scoped EAV generic system form modifier.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class System extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';

    const KEY_RELOAD_URL = 'reloadUrl';

    /**
     * @var \Smile\ScopedEav\Model\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $entityUrls = [
        self::KEY_SUBMIT_URL => '*/*/save',
        self::KEY_RELOAD_URL => '*/*/reload',
    ];

    /**
     * Constructor.
     *
     * @param \Smile\ScopedEav\Model\Locator\LocatorInterface $locator    Entity locator.
     * @param \Magento\Framework\UrlInterface                 $urlBuilder Url builder.
     * @param array                                           $entityUrls Entity urls.
     */
    public function __construct(
        \Smile\ScopedEav\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $entityUrls = []
    ) {
        $this->locator     = $locator;
        $this->urlBuilder  = $urlBuilder;
        $this->entityUrls  = array_replace_recursive($this->entityUrls, $entityUrls);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getEntity();
        $attributeSetId = $model->getAttributeSetId();

        $parameters = ['id' => $model->getId(), 'store' => $model->getStoreId()];

        $actionParameters = array_merge($parameters, ['set' => $attributeSetId]);
        $reloadParameters = array_merge($parameters, ['popup' => 1, 'componentJson' => 1, 'prev_set_id' => $attributeSetId]);

        $submitUrl = $this->urlBuilder->getUrl($this->entityUrls[self::KEY_SUBMIT_URL], $actionParameters);
        $reloadUrl = $this->urlBuilder->getUrl($this->entityUrls[self::KEY_RELOAD_URL], $reloadParameters);

        return array_replace_recursive($data, ['config' => [self::KEY_SUBMIT_URL => $submitUrl, self::KEY_RELOAD_URL => $reloadUrl]]);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
