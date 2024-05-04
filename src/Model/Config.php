<?php

declare(strict_types=1);

namespace Yggverse\Pulsar\Model;

class Config
{
    private object $_config;

    public function __construct(
        string $filename
    ) {
        $this->_config = json_decode(
            file_get_contents(
                realpath(
                    str_starts_with(
                        $filename,
                        DIRECTORY_SEPARATOR
                    ) ? $filename  // absolute
                      : __DIR__ .  // relative
                        DIRECTORY_SEPARATOR . '..'.
                        DIRECTORY_SEPARATOR . '..'.
                        DIRECTORY_SEPARATOR . 'config'.
                        DIRECTORY_SEPARATOR . $filename
                )
            )
        );

        if (!$this->_config)
        {
            throw new \Exception;
        }
    }

    public function get(): object
    {
        return $this->_config;
    }
}