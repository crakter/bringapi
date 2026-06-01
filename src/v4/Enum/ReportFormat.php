<?php

declare(strict_types=1);

namespace Bring\Api\Enum;

/** File-extension suffix for Reports API endpoints (.json, .xml, .xls, .html). */
enum ReportFormat: string
{
    case JSON = 'json';
    case XML = 'xml';
    case XLS = 'xls';
    case HTML = 'html';
}
