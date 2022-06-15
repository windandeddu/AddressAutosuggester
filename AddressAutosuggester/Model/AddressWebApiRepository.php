<?php

namespace WindAndeddu\AddressAutosuggester\Model;

/**
 * Interface AddressWebApiRepositoryInterface
 *
 * @package WindAndeddu\Catalog\Api
 */
class AddressWebApiRepository implements \WindAndeddu\AddressAutosuggester\Api\AddressWebApiRepositoryInterface
{
    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $_pageCacheConfig;

    /**
     * @var \Magento\CacheInvalidate\Model\PurgeCache
     */
    protected $_pageCacheCleaner;

    /**
     * AddressWebApiRepository constructor.
     * @param \Magento\PageCache\Model\Config $pageCacheConfig
     * @param \Magento\CacheInvalidate\Model\PurgeCache $pageCacheCleaner
     */
    public function __construct(
        \Magento\PageCache\Model\Config $pageCacheConfig,
        \Magento\CacheInvalidate\Model\PurgeCache $pageCacheCleaner
    )
    {
        $this->_pageCacheConfig = $pageCacheConfig;
        $this->_pageCacheCleaner = $pageCacheCleaner;
    }

    /**
     * Flush address autosuggester cache
     *
     * @return boolean
     */
    public function flushAddressCache()
    {
        if ($this->_pageCacheConfig->isEnabled() && $this->_pageCacheConfig->getType() == \Magento\PageCache\Model\Config::VARNISH) {
            $this->_pageCacheCleaner->sendPurgeRequest(
                \WindAndeddu\AddressAutosuggester\Model\Client\Config\Config::CITY_CACHE_TAG.'|'
                .\WindAndeddu\AddressAutosuggester\Model\Client\Config\Config::DISTRICT_CACHE_TAG
            );
        }
        return true;
    }

}
