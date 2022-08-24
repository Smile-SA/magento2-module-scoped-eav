<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Phrase;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Helper\Data;

/**
 * Scoped EAV attribute admin abstract controller.
 */
abstract class AbstractAttribute extends Action
{
    /**
     * @var Data
     */
    private $entityHelper;

    /**
     * @var Attribute\BuilderInterface
     */
    private $attributeBuilder;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param Data $entityHelper Entity helper.
     * @param Attribute\BuilderInterface $attributeBuilder Attribute builder.
     */
    public function __construct(
        Context $context,
        Data $entityHelper,
        Attribute\BuilderInterface $attributeBuilder
    ) {
        parent::__construct($context);

        $this->entityHelper     = $entityHelper;
        $this->attributeBuilder = $attributeBuilder;
    }

    /**
     * Return current attribute.
     *
     * @return AttributeInterface
     */
    protected function getAttribute(): AttributeInterface
    {
        return $this->attributeBuilder->build($this->getRequest());
    }

    /**
     * Generate attribute code from label.
     *
     * @param string $label Attribute label.
     *
     * @return string
     */
    protected function generateCode($label): string
    {
        return $this->entityHelper->generateAttributeCodeFromLabel($label);
    }

    /**
     * Create the page.
     *
     * @param Phrase|string $title Page title.
     *
     * @return Page
     */
    protected function createActionPage($title = null): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->_view->getPage();

        $resultPage->initLayout();

        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
            $resultPage->getConfig()->getTitle()->prepend($title);
        }

        return $resultPage;
    }

    /**
     * Redirect to index page on error.
     *
     * @param string $message Error message.
     *
     * @return ResponseInterface
     */
    protected function getRedirectError(string $message): ResponseInterface
    {
        $this->messageManager->addErrorMessage($message);

        return $this->_redirect("*/*");
    }
}
