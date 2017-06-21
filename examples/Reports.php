<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Clients\Reports\GenerateReport;
use Crakter\BringApi\Clients\Reports\GetReport;
use Crakter\BringApi\Clients\Reports\StatusOfReport;
use Crakter\BringApi\Clients\Authorization;
use Crakter\BringApi\Entity\ReportsEntity;

set_time_limit(480);
// Gets the environment variables we have set - you can track without authorization aswell.
$apiKey = getenv('BRING_API_KEY');
$uid = getenv('BRING_UID');
$customerNumber = getenv('BRING_CUSTOMER_NUMBER');
// Your URL
$clientUrl = 'http://example.com';

// Sets the Report to get, passed through terminal like "php PostalCode.php report id"
$reportId = isset($argv[1]) ? $argv[1] : 'PARCELS-PRE_NOTIFICATION_RECEIVED';

/*
 * You can also get the customers by calling the ListAvailableCustomers client
 * use Crakter\BringApi\Clients\Reports\ListAvailableCustomers;
 * $result = (new ListAvailableCustomers)->setAuthorizationModule($credentials)->send();
 */
/*
 * You can also get the report ids by calling the ListAvailableReportsCustomer - then you can pick what you want
 * use Crakter\BringApi\Clients\Reports\ListAvailableReportsCustomer;
 * $result = (new ListAvailableReportsCustomer)->setAuthorizationModule($credentials)->setCustomerId($customerNumber)->send();
 */

// Sets the authorizationModule - you can track without authorization aswell.
$credentials = (new Authorization)
    ->setApiKey($apiKey)
    ->setClientId($uid)
    ->setClientUrl($clientUrl);

// Set from date to date on the report - 5 days is good.
$entity = (new ReportsEntity)->setFromDate((new \DateTime('now'))->modify('-5 days'))->setToDate((new \DateTime('now')));
// Generate the report.
$generateReport = (new GenerateReport)
    ->setAuthorizationModule($credentials)
    ->setCustomerId($customerNumber)
    ->setReportTypeId($reportId)
    ->setApiEntity($entity)
    ->send();
// Get the report id that we get in reponse.
$reportId = $generateReport->getReportId();
// Check the status of the report.
$statusOfReport = (new StatusOfReport)->setAuthorizationModule($credentials)->setReportId($reportId)->send();
// Check the status of the report til it is finished.
while(!$statusOfReport->checkStatus()) {
    sleep(30);
    $statusOfReport->send();
}
// Get the report that is finished.
$getReport = (new GetReport)->setAuthorizationModule($credentials)->setApiEntity($entity)->setReportId($reportId)->send();
// Get the reponse back in Array.
print_r($getReport->toArray());
