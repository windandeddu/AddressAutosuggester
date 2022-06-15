<?php

namespace WindAndeddu\AddressAutosuggester\Model\Client;

class Elasticsearch
{
    /**
     * @var string
     */
    const CITY_SUGGEST_HANDLE = 'city_suggests';

    /**
     * @var string
     */
    const DISTRICT__SUGGEST_HANDLE = 'district_suggests';

    /**
     * @var string
     */
    const CITY_SUGGEST_FIELD = 'city_name_completion';

    /**
     * @var string
     */
    const DISTRICT_SUGGEST_FIELD = 'district_name_completion';

    /**
     * Elasticsearch client
     */
    protected $_client;

    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config
     */
    protected $_config;

    /**
     * Elasticsearch constructor.
     * @param \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config $config
     */
    public function __construct(
        \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config $config
    )
    {
        $this->_config = $config;
    }

    /**
     * Get Elasticsearch Client
     *
     * @return \Elasticsearch\Client
     */
    protected function _getClient($options)
    {
        $pid = getmypid();

        if (!isset($this->_client[$pid])) {
            $config = $this->_prepareClientOptions($options);
            $this->_client[$pid] = \Elasticsearch\ClientBuilder::fromConfig($config, true);
        }
        return $this->_client[$pid];
    }

    /**
     * @param $options
     * @return \Elasticsearch\Client
     */
    protected function _getTestClient($options)
    {
        $config = $this->_prepareClientOptions($options);
        return \Elasticsearch\ClientBuilder::fromConfig($config, true);
    }


    /**
     * @return array
     */
    protected function _buildConfig()
    {
        $options = [
            'hostname' => $this->_config->getElasticsearchHostname(),
            'port' => $this->_config->getElasticsearchPort(),
            'enableAuth' => $this->_config->getElasticsearchEnableAuth(),
            'username' => $this->_config->getElasticsearchUsername(),
            'password' => $this->_config->getElasticsearchPassword(),
            'timeout' => $this->_config::ELASTICSEARCH_DEFAULT_TIMEOUT,
        ];
        return $this->_prepareClientOptions($options);
    }

    /**
     * Build config.
     *
     * @param array $options
     * @return array
     */
    protected function _prepareClientOptions(array $options)
    {
        $hostname = preg_replace('/http[s]?:\/\//i', '', $options['hostname']);
        // @codingStandardsIgnoreStart
        $protocol = parse_url($options['hostname'], PHP_URL_SCHEME);
        // @codingStandardsIgnoreEnd
        if (!$protocol) {
            $protocol = 'http';
        }

        $authString = '';
        if (!empty($options['enableAuth']) && (int)$options['enableAuth'] === 1) {
            $authString = "{$options['username']}:{$options['password']}@";
        }

        $portString = '';
        if (!empty($options['port'])) {
            $portString = ':' . $options['port'];
        }

        $host = $protocol . '://' . $authString . $hostname . $portString;

        $options['hosts'] = [$host];

        return $options;
    }

    /**
     * Execute search by $query
     *
     * @param array $query
     * @return array
     */
    public function query(array $query)
    {
        $options = $this->_buildConfig();
        return $this->_getClient($options)->search($query);
    }

    /**
     * Ping the Elasticsearch client
     *
     * @return bool
     */
    public function testConnection($options)
    {
        $result = $this->_getTestClient($options)->ping();
        return $result;
    }
}
