<?php

namespace WindAndeddu\AddressAutosuggester\Controller\Adminhtml\System;

class TestConnection extends \Magento\Backend\App\Action
{
    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch
     */
    protected $_clientResolver;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    protected $_tagFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch $clientResolver
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch $clientResolver,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filter\StripTags $tagFilter
    )
    {
        parent::__construct($context);
        $this->_clientResolver = $clientResolver;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_tagFilter = $tagFilter;
    }

    /**
     * Check for connection to server
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = [
            'success' => false,
            'errorMessage' => '',
        ];
        $options = $this->getRequest()->getParams();
        $options['engine'] = 'elasticsearch';

        try {
            if (empty($options['engine'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missing search engine parameter.')
                );
            }
            $response = $this->_clientResolver->testConnection($options);
            if ($response) {
                $result['success'] = true;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['errorMessage'] = $e->getMessage();
        } catch (\Exception $e) {
            $message = __($e->getMessage());
            $result['errorMessage'] = $this->_tagFilter->filter($message);
        }

        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($result);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WindAndeddu_AddressAutosuggester::autosuggestion_config');
    }
}
