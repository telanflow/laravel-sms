<?php

namespace Telanflow\Sms\Test;

use Telanflow\Sms\Storage\SessionStorage;

class SessionSmsTest extends SmsTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('sms.storage', SessionStorage::class);
    }
}
