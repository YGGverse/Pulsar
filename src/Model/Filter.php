<?php

declare(strict_types=1);

namespace Yggverse\Pulsar\Model;

class Filter
{
    public static function url(
        string $value
    ): string
    {
        return trim(
            urldecode(
                $value
            )
        );
    }

    public static function title(
        string $value
    ): string
    {
        return trim(
            preg_replace(
                [
                    '/[\n\r]*/',
                    '/[\s]{2,}/',
                ],
                ' ',
                $this->text(
                    $value
                )
            )
        );
    }

    public static function description(
        string $value
    ): string
    {
        return $this->text(
            $value
        );
    }

    public static function text(
        string $value
    ): string
    {
        return trim(
            preg_replace(
                [
                    '/[\n\r]{2,}/',
                    '/[\s]{2,}/',
                ],
                [
                    PHP_EOL,
                    ' '
                ],
                strip_tags(
                    html_entity_decode(
                        $value
                    )
                )
            )
        );
    }
}