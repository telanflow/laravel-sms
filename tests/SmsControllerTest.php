<?php

namespace Telanflow\Sms\Test;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

class SmsControllerTest extends TestCase
{
    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Telanflow\Sms\ServiceProvider'];
    }

    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Sms' => "Telanflow\Sms\Facade",
        ];
    }

    public function testPostSendCode()
    {
        //1. test success mobile.
        $response = $this->post('sms/verify-code', ['mobile' => '18973305743']);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => true, 'message' => '短信发送成功']);

        //2. test repeat in 60 seconds.
        $response = $this->post('sms/verify-code', ['mobile' => '18973305743']);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => false, 'message' => '每60秒发送一次']);

        //3. test invalid mobile.
        $response = $this->post('sms/verify-code', ['mobile' => '10000000000']);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => false, 'message' => '无效手机号码']);
    }

    public function testInfo()
    {
        $response = $this->get('sms/info?mobile=18988885555');

        $response
            ->assertStatus(200);
    }

    public function testDebug()
    {
        $this->app['config']->set('app.debug', true);

        $response = $this->get('sms/info?mobile=18988885555');
        $response
            ->assertStatus(200);
    }
}
