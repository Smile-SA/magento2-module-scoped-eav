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

namespace Smile\ScopedEav\Ui\Component\Attribute\Listing\Column;

/**
 * Scoped EAV entity attribute grid actions column.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            Context.
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory UI Components factory.
     * @param \Magento\Framework\UrlInterface                              $urlBuilder         URL Builder.
     * @param array                                                        $components         UI Components.
     * @param array                                                        $data               Additional data.
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare action column content.
     *
     * @param array $dataSource Row data.
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $this->getEditUrl($item);
                if (isset($item['attribute_id'])) {
                    $item[$this->getData('name')] = ['edit' => ['href'  => $this->getEditUrl($item), 'label' => __('Edit')]];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Return edit URL for a row.
     *
     * @param array $item Item data.
     *
     * @return string
     */
    private function getEditUrl($item)
    {
        $editUrlPath = $this->getDataByPath('config/editUrlPath');

        return $this->urlBuilder->getUrl($editUrlPath, ['attribute_id' => $item['attribute_id']]);
    }
}
