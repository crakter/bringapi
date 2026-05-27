<?php

declare(strict_types=1);

namespace Bring\Api\Exception;

/**
 * Network-level failure (connection refused, DNS, TLS, timeout) — i.e. the
 * underlying PSR-18 client threw. The Bring server itself never replied.
 */
final class BringTransportException extends \RuntimeException implements BringException
{
}
