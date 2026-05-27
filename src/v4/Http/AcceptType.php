<?php

declare(strict_types=1);

namespace Bring\Api\Http;

enum AcceptType: string
{
    case JSON = 'application/json';
    case XML = 'application/xml';
    case XLS = 'application/vnd.ms-excel';
    case PNG = 'image/png';
    case HTML = 'text/html';

    public function extension(): string
    {
        return match ($this) {
            self::JSON => 'json',
            self::XML => 'xml',
            self::XLS => 'xls',
            self::PNG => 'png',
            self::HTML => 'html',
        };
    }
}
