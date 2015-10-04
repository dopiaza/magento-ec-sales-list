<?php

/**
 * Created by PhpStorm.
 * User: davidw
 * Date: 03/10/2015
 * Time: 11:51
 */

require_once 'abstract.php';

class Dopiaza_Shell_VAT_Report extends Mage_Shell_Abstract
{
    const QUOTE = "'";
    const TXT_SEPARATOR = "\t";
    const CSV_SEPARATOR = ',';
    const EOL = "\n";


    /**
     * Run script
     *
     * @return void
     */
    public function run()
    {
        $fromArg = $this->getArg('from');
        $toArg = $this->getArg('to');
        $format = $this->getArg('format');

        $from = empty($fromArg) ? new \DateTime() : \DateTime::createFromFormat('Y-m-d', $fromArg);
        $to = empty($toArg) ? new \DateTime() : \DateTime::createFromFormat('Y-m-d', $toArg);

        if (empty($format))
        {
            $format = 'txt';
        }

        $report = Mage::getModel('dopiazavat/sales_ecsaleslist');
        $report->setFrom($from);
        $report->setTo($to);
        $report->run();

        $data = $report->getAmounts();

        foreach ($data as $vatId => $amount)
        {
            if ($format == 'csv')
            {
                echo self::QUOTE . $vatId . self::QUOTE .
                    self::CSV_SEPARATOR .
                    self::QUOTE . sprintf('%.2f', $amount) . self::QUOTE .
                    self::EOL;
            }
            else
            {
                echo $vatId . self::TXT_SEPARATOR . sprintf('%.2f', $amount) . self::EOL;
            }

        }
    }

    /**
     * Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f ecsales.php -- [options]

  --from <date>             From date
  --to <dater>              To date
  --format <format>         txt or csv

USAGE;
    }
}

$shell = new Dopiaza_Shell_VAT_Report();
$shell->run();
