<?php

namespace Telanflow\Sms\Test;

use Telanflow\Sms\Storage\CacheStorage;

/**
 * Class SmsTest.
 */
class CacheSmsTest extends SmsTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('sms.storage', CacheStorage::class);
    }
}
