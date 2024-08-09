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
namespace Crakter\BringApi\Clients;

/**
 * BringApi ClientsInterface
 *
 * A Interface for Client classes to implement
 *
 * Quick setup: <code>class ReportsGenerateReport extends Base implements ClientsInterface {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
interface ClientsInterface
{
    /**
     * Process the Client Url Variables
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function processClientUrlVariables(): ClientsInterface;

    /**
     * Check if response has errors, we need to specify for each client as Bring does not standardlize the output of error.
     * @throws BringClientException
     * @return ClientsInterface     All clients must implement ClientsInterface
     */
    public function checkErrors(): ClientsInterface;

    /**
     * Process the entities and send correct info to Bring Api depending on client.
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function processEntity(): ClientsInterface;
}
