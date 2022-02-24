<?php

namespace Telanflow\Sms;

use Carbon\Carbon;
use Exception;
use Telanflow\Sms\Jobs\DbLogger;
use Telanflow\Sms\Messages\CodeMessage;
use Telanflow\Sms\Storage\CacheStorage;
use Telanflow\Sms\Storage\StorageInterface;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Sms
{
	/**
	 * @var EasySms
	 */
	protected $easySms;
	/**
	 * @var
	 */
	protected $storage;

	/**
	 * @var
	 */
	protected $key;

	/**
	 * @param mixed $key
	 */
	public function setKey($key)
	{
		$key       = 'sms.' . $key;
		$this->key = md5($key);
	}

	/**
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->key;
	}

    /**
     * Sms constructor.
     *
     * @param EasySms $easySms
     * @param StorageInterface $storage
     */
	public function __construct(EasySms $easySms, StorageInterface $storage)
	{
		$this->easySms = $easySms;
		$this->storage = $storage;
	}

	/**
	 * @param StorageInterface $storage
	 */
	public function setStorage(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	/**
     * send sms to mobile
     *
	 * @param string $mobile
	 * @param null  $data
	 * @param array $gateways
	 * @return bool
	 */
	public function send($mobile, $data = [], array $gateways = []): bool
    {
		try {
			$flag = false;

			$this->setKey($mobile);

			// 1. get code from storage.
			$code = $this->getCodeFromStorage();

			if ($this->needNewCode($code)) {
				$code = $this->getNewCode($mobile);
			}

			$validMinutes = (int) config('sms.code.validMinutes', 5);

			if (!($data instanceof MessageInterface)) {
				$message = new CodeMessage($code->code, $validMinutes, $data);
			} else {
				$message = $data;
			}

			$results = $this->easySms->send($mobile, $message, $gateways);

			foreach ($results as $value)
            {
				if ('success' == $value['status']) {
					$code->put('sent', true);
					$code->put('sentAt', Carbon::now());
					$this->storage->set($this->key, $code);
					$flag = true;
				}
			}
		} catch (NoGatewayAvailableException $noGatewayAvailableException) {
			$results = $noGatewayAvailableException->results;
			$flag    = false;
		} catch (Exception $exception) {
			$results = $exception->getMessage();
			$flag    = false;
		}

		DbLogger::dispatch($code, json_encode($results), $flag);

		return $flag;
	}

	/**
	 * check china mobile.
	 *
	 * @param string $mobile
	 *
	 * @return false|int
	 */
	public function verifyMobile($mobile)
	{
		return preg_match('/^(?:\+?86)?1(?:3\d{3}|5[^4\D]\d{2}|8\d{3}|7(?:[0-35-9]\d{2}|4(?:0\d|1[0-2]|9\d))|9[0-35-9]\d{2}|6[2567]\d{2}|4(?:(?:10|4[01])\d{3}|[68]\d{4}|[579]\d{2}))\d{6}$/', $mobile);
	}

	/**
	 * @return mixed
	 */
	public function getCodeFromStorage()
	{
		return $this->storage->get($this->key, '');
	}

	/**
	 * @param $code
	 *
	 * @return bool
	 */
	protected function needNewCode($code)
	{
		if (empty($code)) {
			return true;
		}

		return $this->checkAttempts($code);
	}

	/**
	 * Check attempt times.
	 *
	 * @param $code
	 *
	 * @return bool
	 */
	private function checkAttempts($code)
	{
		$maxAttempts = config('sms.code.maxAttempts');

		if ($code->expireAt > Carbon::now() && $code->attempts < $maxAttempts) {
			return false;
		}

		return true;
	}

	/**
	 * @param $to
	 *
	 * @return Code
	 */
	public function getNewCode($to)
	{
		$code = $this->generateCode($to);

		$this->storage->set($this->key, $code);

		return $code;
	}

	/**
	 * @param $to
	 *
	 * @return bool
	 */
	public function canSend($to)
	{
		$this->setKey($to);

		$code = $this->storage->get($this->key, '');

		if (empty($code) || $code->sentAt < Carbon::now()->addMinutes(-1)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $to
	 *
	 * @return Code
	 */
	public function generateCode($mobile)
	{
		$length       = (int) config('sms.code.length', 5);
		$characters   = '0123456789';
		$charLength   = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; ++$i) {
			$randomString .= $characters[mt_rand(0, $charLength - 1)];
		}

		$validMinutes = (int) config('sms.code.validMinutes', 5);

		return new Code($mobile, $randomString, false, 0, Carbon::now()->addMinutes($validMinutes));
	}

	/**
	 * @return CacheStorage|StorageInterface
	 */
	public function getStorage()
	{
		return $this->storage ?: new CacheStorage();
	}

	/**
	 * @param $to
	 * @param $inputCode
	 *
	 * @return bool
	 */
	public function checkCode($to, $inputCode)
	{
		if (config('app.debug')) {
			return true;
		}

		$this->setKey($to);

		$code = $this->storage->get($this->key, '');

		if (empty($code)) {
			return false;
		}

		if ($code && $code->code == $inputCode) {
			$this->storage->forget($this->key);

			return true;
		}

		$code->put('attempts', $code->attempts + 1);

		$this->storage->set($this->key, $code);

		return false;
	}
}
