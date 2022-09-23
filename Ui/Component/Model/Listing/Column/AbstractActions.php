<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Model\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Scoped EAV abstract grid actions column.
 */
abstract class AbstractActions extends Column
{
    private UrlInterface $urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface $context Context.
     * @param UiComponentFactory $uiComponentFactory UI Components factory.
     * @param UrlInterface $urlBuilder URL Builder.
     * @param array $components UI Components.
     * @param array $data Additional data.
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
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
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $this->getEditUrl($item);
                if (isset($item[$this->getIndexField()])) {
                    $item[$this->getData('name')] =
                        ['edit' => ['href'  => $this->getEditUrl($item), 'label' => __('Edit')]];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Return edit URL for a row.
     *
     * @param array $item Item data.
     */
    protected function getEditUrl(array $item): string
    {
        $editUrlPath = $this->getDataByPath('config/editUrlPath');

        return $this->urlBuilder->getUrl($editUrlPath, [$this->getRequestFieldName() => $item[$this->getIndexField()]]);
    }

    /**
     * Return index field name.
     */
    protected function getIndexField(): string
    {
        return $this->getDataByPath('config/indexField');
    }

    /**
     * Return request field name.
     */
    abstract protected function getRequestFieldName(): string;
}
