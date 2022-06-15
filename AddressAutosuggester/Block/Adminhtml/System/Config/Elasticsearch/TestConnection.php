<?php
namespace WindAndeddu\AddressAutosuggester\Block\Adminhtml\System\Config\Elasticsearch;

class TestConnection extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @return string[]
     */
    public function _getFieldMapping()
    {
        $fields = [
            'hostname' => 'elasticsearch_autosuggestion_elasticsearch_server_hostname',
            'port' => 'elasticsearch_autosuggestion_elasticsearch_server_port',
            'enableAuth' => 'elasticsearch_autosuggestion_elasticsearch_elasticsearch_enable_auth',
            'username' => 'elasticsearch_autosuggestion_elasticsearch_elasticsearch_username',
            'password' => 'elasticsearch_autosuggestion_elasticsearch_elasticsearch_password',
        ];

        return $fields;
    }

    /**
     * @return $this|TestConnection
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('WindAndeddu_AddressAutosuggester::system/config/testconnection.phtml');
        return $this;
    }


    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element = clone $element;
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }


    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('address_autosuggester/system/testconnection'),
                'field_mapping' => str_replace('"', '\\"', json_encode($this->_getFieldMapping()))
            ]
        );

        return $this->_toHtml();
    }

}
