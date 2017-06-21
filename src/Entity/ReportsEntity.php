<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity;

use Crakter\BringApi\DefaultData\ValidateParameters;

/**
 * BringApi ReportsEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$reports = (new ReportsEntity)
 *                     ->setRequiredParameters(ReportsListAvailableReportsCustomer->getParameters());</code>
 *
 * @property string $selectedCriteria This should be either FIXED_DATE_RANGE for fixed data range or INVOICE_NUMBER for the report PARCELS-ADDITIONAL-COSTS
 * @property string $fromDate         This is used on most reports format DD.MM.YYY
 * @property string $toDate           This is used on most reports format DD.MM.YYY
 * @property string $invoiceNumber    This is used on some reports
 * @property string $reporttype       This is used on some reports  (can be INVOICED)
 * @property string $year             This is used on some reports
 * @method ApiEntityInterface setFromDate(string $string)
 * @method string getFromDate()
 * @method ApiEntityInterface setSelectedCriteria(string $string)
 * @method string getSelectedCriteria()
 * @method ApiEntityInterface setToDate(string $string)
 * @method string getToDate()
 * @method ApiEntityInterface setInvoiceNumber(string $string)
 * @method string getInvoiceNumber()
 * @method ApiEntityInterface setReportType(string $string)
 * @method string getReportType()
 * @method ApiEntityInterface setYear(string $string)
 * @method string getYear()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ReportsEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * Sets the required Parameters, if you need extra checking.
     * @param  array              $parameters list of required variables from ListAvailableReportsCustomer
     * @return ApiEntityInterface
     */
    public function setRequiredParameters(array $parameters): ApiEntityInterface
    {
        foreach ($parameters as $param) {
            $this->setValidateParameter($param['name'], ValidateParameters::NOT_NULL);
        }

        return $this;
    }

    /**
     * Common for most reports
     * @param  DateTime           $date
     * @return ApiEntityInterface
     */
    public function setFromDate(\DateTime $date): ApiEntityInterface
    {
        $this->fromDate = $date->format('d.m.Y');

        return $this;
    }

    /**
     * Common for most reports
     * @param  DateTime           $date
     * @return ApiEntityInterface
     */
    public function setToDate(\DateTime $date): ApiEntityInterface
    {
        $this->toDate = $date->format('d.m.Y');

        return $this;
    }
}
