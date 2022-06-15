<?php
declare(strict_types=1);

namespace WindAndeddu\AddressAutosuggesterGraphQl\Model\Resolver\Elasticsearch;

class CityIdentity implements \Magento\Framework\GraphQl\Query\Resolver\IdentityInterface
{

    /**
     * @param array $resolvedData
     * @return array
     */
    public function getIdentities(array $resolvedData): array
    {
        $identities = [\WindAndeddu\AddressAutosuggester\Model\Client\Config\Config::CITY_CACHE_TAG];
        return array_unique($identities);
    }
}
