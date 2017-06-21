<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$dir = __DIR__ . '/src';

$iterator = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('build')
    ->exclude('tests')
    ->in($dir);

$options = [
    'theme' => 'default',
    'title' => 'Bring PHP API Documentation',
    'build_dir' => __DIR__ . '/docs/build',
    'cache_dir' => __DIR__ . '/docs/cache',
];

$sami = new Sami\Sami($iterator, $options);

return $sami;
