<?php

declare(strict_types=1);

namespace Bring\Api\Enum;

/** Accept-Language values understood by Bring (used by Shipping Guide, Tracking). */
enum Language: string
{
    case NORWEGIAN_BOKMAL = 'no';
    case ENGLISH = 'en';
    case SWEDISH = 'sv';
    case DANISH = 'da';
    case FINNISH = 'fi';
}
