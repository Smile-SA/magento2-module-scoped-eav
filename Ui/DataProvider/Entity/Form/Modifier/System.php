<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

use Magento\Framework\UrlInterface;
use Smile\ScopedEav\Model\Locator\LocatorInterface;

/**
 * Scoped EAV generic system form modifier.
 */
class System extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';

    const KEY_RELOAD_URL = 'reloadUrl';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $defaultEntityUrls = [
        self::KEY_SUBMIT_URL => '*/*/save',
        self::KEY_RELOAD_URL => '*/*/reload',
    ];

    /**
     * @var array
     */
    private $entityUrls;

    /**
     * Constructor.
     *
     * @param LocatorInterface $locator Entity locator.
     * @param UrlInterface $urlBuilder Url builder.
     * @param array  $entityUrls Entity urls.
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        array $entityUrls = []
    ) {
        $this->locator     = $locator;
        $this->urlBuilder  = $urlBuilder;
        $this->entityUrls  = $entityUrls;
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

        $submitUrl = $this->urlBuilder->getUrl($this->getEntityUrl(self::KEY_SUBMIT_URL), $actionParameters);
        $reloadUrl = $this->urlBuilder->getUrl($this->getEntityUrl(self::KEY_RELOAD_URL), $reloadParameters);

        return array_replace_recursive($data, ['config' => [self::KEY_SUBMIT_URL => $submitUrl, self::KEY_RELOAD_URL => $reloadUrl]]);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Return entity url by url type.
     *
     * @param string $urlType Url type.
     *
     * @return string
     */
    private function getEntityUrl(string $urlType) : string
    {
        return (string) ($this->entityUrls[$urlType] ?? $this->defaultEntityUrls[$urlType]);
    }
}
