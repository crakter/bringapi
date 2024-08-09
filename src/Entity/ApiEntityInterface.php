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
namespace Crakter\BringApi\Entity;

/**
 * BringApi ApiEntityInterface
 *
 * An Interface for Entity classes to implement
 *
 * Quick setup: <code>class ReportsEntity extends ApiEntityBase implements ApiEntityInterface {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
interface ApiEntityInterface
{
    /**
     * Set Required Parameters
     */
    public function setRequiredParameters(array $parameters): ApiEntityInterface;
}
