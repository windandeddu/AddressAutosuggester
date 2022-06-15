<?php

namespace WindAndeddu\AddressAutosuggester\Model\Client\Config;

class Config
{
    /**
     * City cache tag
     */
    const CITY_CACHE_TAG = 'city_autosuggester';

    /**
     * District cache tag
     */
    const DISTRICT_CACHE_TAG = 'district_autosuggester';

    /**
     * Default Elasticsearch server timeout
     */
    const ELASTICSEARCH_DEFAULT_TIMEOUT = 15;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     * @since 100.1.0
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve information from Elasticsearch search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     * @since 100.1.0
     */
    protected function _getElasticsearchConfigData($field, $storeId = null)
    {
        $path = 'elasticsearch_autosuggestion/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return int|string
     */
    public function getElasticsearchHostname()
    {
        return $this->_getElasticsearchConfigData('elasticsearch/server_hostname');
    }

    /**
     * @return int|string
     */
    public function getElasticsearchPort()
    {
        return $this->_getElasticsearchConfigData('elasticsearch/server_port');

    }

    /**
     * @return int|string
     */
    public function getElasticsearchEnableAuth()
    {
        return $this->_getElasticsearchConfigData('elasticsearch/elasticsearch_enable_auth');

    }

    /**
     * @return int|string
     */
    public function getElasticsearchUsername()
    {
        return $this->_getElasticsearchConfigData('elasticsearch/elasticsearch_username');

    }

    /**
     * @return int|string
     */
    public function getElasticsearchPassword()
    {
        return $this->_getElasticsearchConfigData('elasticsearch/elasticsearch_password');

    }

    /**
     * @return int|string
     */
    public function getSuggestionCountry()
    {
        return $this->_getElasticsearchConfigData('address_suggester/country');

    }

    /**
     * @return int|string
     */
    public function getDistrictSuggestionIndex()
    {
        return $this->_getElasticsearchConfigData('address_suggester/district_suggestion_index');

    }

    /**
     * @return int|string
     */
    public function getCitySuggestionIndex()
    {
        return $this->_getElasticsearchConfigData('address_suggester/city_suggestion_index');

    }
}
