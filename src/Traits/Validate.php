<?php

declare(strict_types=1);

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Crakter\BringApi\Traits;

use ReflectionClass;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

/**
 * BringApi Validate trait
 *
 * Checks if constant is available in class
 *
 * Quick example: <code>{CLASS}::has('JSON')</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
trait Validate
{
    /**
     * Check if Constant is defined in class
     * @param  string $name name of Constant or value of constant
     * @return bool   true if exists, false if not
     */
    public static function has(string $name): bool
    {
        $class = new ReflectionClass(self::class);

        return $class->hasConstant(strtoupper($name)) ?: in_array($name, $class->getConstants());
    }

    /**
     * Get constant from class if exists
     * @param  string                        $name name of Constant or value of constant
     * @throws InputValueNotAllowedException if value does not existing
     * @return string                        name of right value
     */
    public static function get(string $name): string
    {
        $class = new ReflectionClass(self::class);
        if (!self::has($name)) {
            throw new InputValueNotAllowedException(
                sprintf('$name(%s) is not allowed by Bring API in %s', $name, $class->getName())
            );
        }
        $class = new ReflectionClass(self::class);

        return $class->getConstant(strtoupper($name)) ?: $name;
    }
}
