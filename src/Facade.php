<?php

namespace Telanflow\Sms;

use Telanflow\Sms\Storage\CacheStorage;
use Telanflow\Sms\Storage\StorageInterface;

/**
 * SMS Facade.
 *
 * @method static void setKey($key)
 * @method static mixed getKey()
 * @method static void setStorage(StorageInterface $storage)
 * @method static CacheStorage|StorageInterface getStorage()
 * @method static bool send($mobile, $data = [], array $gateways = [])
 * @method static false|int verifyMobile($mobile)
 * @method static mixed getCodeFromStorage()
 * @method static Code getNewCode()
 * @method static bool canSend($value)
 * @method static Code generateCode($mobile)
 * @method static bool checkCode($mobile, $inputCode)
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Sms::class;
    }
}
