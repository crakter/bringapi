<?php

declare(strict_types=1);

namespace Bring\Api\Enum;

/**
 * ISO-3166-1 alpha-2 country codes supported by Bring's APIs.
 *
 * Bring does not accept every ISO country in every endpoint — Pickup Point
 * for example only serves NO/SE/DK/FI. Endpoint-level validation lives on
 * each request DTO.
 */
enum Country: string
{
    case NO = 'NO';
    case SE = 'SE';
    case DK = 'DK';
    case FI = 'FI';
    case NL = 'NL';
    case DE = 'DE';
    case US = 'US';
    case BE = 'BE';
    case FO = 'FO';
    case GL = 'GL';
    case IS = 'IS';
    case SJ = 'SJ';
    case GB = 'GB';
    case FR = 'FR';
    case ES = 'ES';
    case IT = 'IT';
    case PL = 'PL';

    public function name3166Alpha2(): string
    {
        return $this->value;
    }
}
