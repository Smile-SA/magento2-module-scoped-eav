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

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set admin save controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Save extends \Smile\ScopedEav\Controller\Adminhtml\AbstractSet
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context                $context                Context.
     * @param \Magento\Framework\Registry                        $registry               Registry.
     * @param \Magento\Eav\Model\Config                          $eavConfig              EAV config.
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface   $attributeSetRepository Attribute set repository.
     * @param \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory    Attribute set factory.
     * @param \Magento\Framework\Filter\FilterManager            $filterManager          Filters.
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper             JSON helper.
     * @param \Magento\Framework\Controller\Result\JsonFactory   $resultJsonFactory      Result JSON factory.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $registry, $eavConfig, $attributeSetRepository, $attributeSetFactory);

        $this->filterManager       = $filterManager;
        $this->jsonHelper          = $jsonHelper;
        $this->resultJsonFactory   = $resultJsonFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $hasError = false;
        $isNewSet = $this->getRequest()->getParam('gotoEdit', false) == '1';

        $attributeSet = $this->getAttributeSet();

        try {
            $attributeSet->setAttributeSetName($this->filterManager->stripTags($this->getRequest()->getParam('attribute_set_name')));

            if ($isNewSet === false) {
                $data = $this->jsonHelper->jsonDecode($this->getRequest()->getPost('data'));
                $data['attribute_set_name'] = $this->filterManager->stripTags($data['attribute_set_name']);
                $attributeSet->organizeData($data);
            }

            $attributeSet->validate();

            if ($isNewSet) {
                $attributeSet->save();
                $attributeSet->initFromSkeleton($this->getRequest()->getParam('skeleton_set'));
            }

            $attributeSet->save();
            $this->messageManager->addSuccessMessage(__('You saved the attribute set.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the attribute set.'));
            $hasError = true;
        }

        $response = $hasError ? $this->getErrorResponse() : $this->getSuccessResponse();

        if ($isNewSet) {
            $response = $this->getNewAttributeSetResponse($attributeSet);
        }

        return $response;
    }

    /**
     * Redirect user on new attribute set save.
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet Attribute set.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    private function getNewAttributeSetResponse($attributeSet)
    {
        $resultRedirect = $this->_redirect('*/*/add');

        if ($attributeSet && $attributeSet->getId()) {
            $resultRedirect = $this->_redirect('*/*/edit', ['id' => $attributeSet->getId()]);
        }

        return $resultRedirect;
    }

    /**
     * Return formatted JSON success response.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function getSuccessResponse()
    {
        $response = ['error' => 0, 'url' => $this->getUrl('*/*/index')];

        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * Return formatted JSON error response.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function getErrorResponse()
    {
        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $response = ['error' => 1, 'message' => $layout->getMessagesBlock()->getGroupedHtml()];

        return $this->resultJsonFactory->create()->setData($response);
    }
}
