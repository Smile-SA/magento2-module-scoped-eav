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

namespace Smile\ScopedEav\Controller\Adminhtml;

/**
 * Scoped EAV attribute admin abstract controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
abstract class AbstractAttribute extends \Magento\Backend\App\Action
{
    /**
     * @var \Smile\ScopedEav\Helper\Data
     */
    private $entityHelper;

    /**
     * @var Attribute\BuilderInterface
     */
    private $attributeBuilder;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context          Context.
     * @param \Smile\ScopedEav\Helper\Data        $entityHelper     Entity helper.
     * @param Attribute\BuilderInterface          $attributeBuilder Attribute builder.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Smile\ScopedEav\Helper\Data $entityHelper,
        Attribute\BuilderInterface $attributeBuilder
    ) {
        parent::__construct($context);

        $this->entityHelper     = $entityHelper;
        $this->attributeBuilder = $attributeBuilder;
    }

    /**
     * Return current attribute.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterface
     */
    protected function getAttribute()
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
    protected function generateCode($label)
    {
        return $this->entityHelper->generateAttributeCodeFromLabel($label);
    }

    /**
     * Create the page.
     *
     * @param \Magento\Framework\Phrase|null $title Page title.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
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
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function getRedirectError($message)
    {
        $this->messageManager->addErrorMessage($message);

        return $this->_redirect("*/*");
    }
}
