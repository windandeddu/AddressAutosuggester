<?php

namespace WindAndeddu\AddressAutosuggesterGraphQl\Model\Resolver;

class District extends \WindAndeddu\AddressAutosuggesterGraphQl\Model\Resolver\City implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch
     */
    protected $_client;

    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config
     */
    protected $_config;

    /**
     * District constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch $client
     * @param \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config $config
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch $client,
        \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config $config
    )
    {
        $this->_client = $client;
        $this->_config = $config;
        parent::__construct($storeManager, $client, $config);
    }

    /**
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array[]|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        if (!isset($args['input']['city_phrase']) || empty($args['input']['city_phrase'])) {
            throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(__("'city_phrase' input argument is required."));
        }
        if (!isset($args['input']['district_phrase']) || empty($args['input']['district_phrase'])) {
            throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(__("'district_phrase' input argument is required."));
        }
        $districtsOutput = $this->_getDistrictsResponse($args['input']['district_phrase'], $args['input']['city_phrase']);

        return $districtsOutput;
    }


    /**
     * @param $phrase
     * @param $cityName
     * @return array|array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getDistrictsResponse($phrase, $cityName)
    {
        $districtSuggestionIndex = $this->_config->getDistrictSuggestionIndex();
        $result = ['districts' => []];
        if (empty($districtSuggestionIndex)) {
            return $result;
        }

        $cities = $this->_getCitiesResponse($cityName);
        $cityIds = [];
        if (empty($cities['cityIds'])) {
            return $result;
        } else {
            $cityIds = $cities['cityIds'];
        }

        $queryScheme = [
            'index' => $districtSuggestionIndex,
            'body' => [
                'suggest' => [
                    $this->_client::DISTRICT__SUGGEST_HANDLE => [
                        'prefix' => $phrase,
                        'completion' => [
                            'field' => $this->_client::DISTRICT_SUGGEST_FIELD,
                            'fuzzy' => [
                                'fuzziness' => 1,
                                'min_length' => 5
                            ],
                            'contexts' => [
                                'city_ids' => $cityIds,
                            ],
                            'size' => 10,
                        ],
                    ],
                ]
            ]
        ];
        $data = $this->_client->query($queryScheme);

        if (!isset($data['suggest'], $data['suggest'][$this->_client::DISTRICT__SUGGEST_HANDLE])) {
            return $result;
        }

        foreach ($data['suggest'][$this->_client::DISTRICT__SUGGEST_HANDLE] as $suggests) {
            if ($suggests['options']) {
                foreach ($suggests['options'] as $option) {
                    $result['districts'][] = $option['text'];
                }
            }
        }
        return $result;
    }
}
