<?php
declare(strict_types=1);

namespace WindAndeddu\AddressAutosuggesterGraphQl\Model\Resolver\Elasticsearch;

class DistrictIdentity implements \Magento\Framework\GraphQl\Query\Resolver\IdentityInterface
{

    /**
     * @param array $resolvedData
     * @return array
     */
    public function getIdentities(array $resolvedData): array
    {
        $identities = [\WindAndeddu\AddressAutosuggester\Model\Client\Config\Config::DISTRICT_CACHE_TAG, \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config::CITY_CACHE_TAG];
        return array_unique($identities);
    }
}
