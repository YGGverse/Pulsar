<?php

declare(strict_types=1);

namespace Yggverse\Pulsar\Model;

class Environment
{
    private array $_argument;

    public function __construct(
        array $argv
    ) {
        foreach ($argv as $value)
        {
            if (preg_match('/^(?<key>[^=]+)=(?<value>.*)$/', $value, $argument))
            {
                $this->_argument[mb_strtolower($argument['key'])] = (string) $argument['value'];
            }
        }
    }

    public function get(
        string $key
    ): ?string
    {
        $key = mb_strtolower(
            $key
        );

        return isset($this->_argument[$key]) ? $this->_argument[$key]
                                             : null;
    }
}