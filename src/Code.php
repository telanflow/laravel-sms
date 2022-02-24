<?php

namespace Telanflow\Sms;

use Illuminate\Support\Collection;

class Code extends Collection
{
    /**
     * Code constructor.
     *
     * @param $to
     * @param $code
     * @param $sent
     * @param $attempts
     * @param $expireAt
     */
    public function __construct($to, $code, $sent, $attempts, $expireAt)
    {
        parent::__construct([
            'to' => $to,
            'code' => $code,
            'sent' => $sent,
            'attempts' => $attempts,
            'expireAt' => $expireAt,
        ]);
    }

    /**
     * Magic accessor.
     *
     * @param string $property property name
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($this->has($property)) {
            return $this->get($property);
        }
    }
}
