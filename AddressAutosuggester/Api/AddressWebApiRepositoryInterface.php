<?php
namespace WindAndeddu\AddressAutosuggester\Api;

/**
 * Interface AddressWebApiRepositoryInterface
 *
 * @package WindAndeddu\Catalog\Api
 */
interface AddressWebApiRepositoryInterface{

    /**
     * Flush address autosuggester cache
     *
     * @return boolean
     */
    public function flushAddressCache();
}
