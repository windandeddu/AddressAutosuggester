<?php
declare(strict_types=1);

namespace WindAndeddu\AddressAutosuggesterGraphQl\Model\Resolver;


class City implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Elasticsearch
     */
    protected $_client;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config
     */
    protected $_config;


    /**
     * City constructor.
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
        $this->_storeManager = $storeManager;
        $this->_client = $client;
        $this->_config = $config;
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
        if (empty($args['input']['city_phrase'])) {
            throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(__("'city_phrase' input argument is required."));
        }

        $citiesOutput = $this->_getCitiesResponse($args['input']['city_phrase']);
        return ['cities' => $citiesOutput['cities']];
    }

    /**
     * @param $phrase
     * @return array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getCitiesResponse($phrase)
    {
        $country = $this->_storeManager->getStore()->getCurrentCountryCode();
        $allowedCountries = explode(',', $this->_config->getSuggestionCountry());
        $citySuggestionIndex = $this->_config->getCitySuggestionIndex();
        $result = ['cities' => [], 'cityIds' => []];
        if (empty($citySuggestionIndex) || !in_array($country, $allowedCountries)) {
            return $result;
        }

        $queryScheme = [
            'index' => $citySuggestionIndex,
            'body' => [
                'suggest' => [
                    $this->_client::CITY_SUGGEST_HANDLE => [
                        'prefix' => $phrase,
                        'completion' => [
                            'field' => $this->_client::CITY_SUGGEST_FIELD,
                            'fuzzy' => [
                                'fuzziness' => 1,
                                'min_length' => 4
                            ],
                            'contexts' => [
                                'country_ids' => $country,
                            ],
                            'size' => 10,
                        ],
                    ]
                ]
            ]
        ];
        $data = $this->_client->query($queryScheme);

        if (!isset($data['suggest'], $data['suggest'][$this->_client::CITY_SUGGEST_HANDLE])) {
            return $result;
        }

        foreach ($data['suggest'][$this->_client::CITY_SUGGEST_HANDLE] as $suggests) {
            if ($suggests['options']) {
                foreach ($suggests['options'] as $option) {
                    $result['cities'][] = $option['text'];
                    $result['cityIds'][] = $option['_id'];
                }
            }
        }

        return $result;
    }
}
