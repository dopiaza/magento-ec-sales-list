#Magento EC Sales List

This Magento extension extracts the data required to generate the [EC Sales list] (https://www.gov.uk/guidance/vat-how-to-report-your-eu-sales) required for reporting VAT in the UK.

It takes all of the invoices and credit memos that were issued during the period for any order that specified a valid VAT ID as part of their shipping address, and generates an aggregate total for each VAT ID. The date used by this extension is the creation date for each invoice and credit memo. Tranactions should be included on the EC Sales list based on the date the order was placed, rather than the date the payment was received. This extension currently uses the invoice date rather than the order date. If you accept payment methods that may cause the invoice to be generated on a different day to the day the order was placed (cheque, bank transfer, etc), then this extension may not work for you in its current form. This is something I'll look at fixing in the near future.

This extension checks the `vat_is_valid` flag held against the shipping address. If you are not using the [VIES service](http://ec.europa.eu/taxation_customs/vies/faqvies.do) to ensure that your VAT ids are valid, this extension probably won't work for you.

Install using [modman](https://github.com/colinmollenhour/modman) or by simply copying the files into the corresponding directories in your Magento installation.

The report is generated from the command line. Change directory to the `shell` directory in the top of your Magento installation and run the command:

```
php -f ecsales.php -- --from 2015-01-01 --to 2015-03-31 --format txt
```

`from` and `to` dates must be specified in the format `YYYY-MM-DD` and will default to today if not specified. `format` may be either `txt` or `csv` and will default to `txt` if not specified.

## Magenta Version Support

This extension has been tested with Magento version 1.9.2.1. It should work with other recent versions of Magento, but you should (as always) verify it is working as expected before deploying to a production server.

## To Do

Use order date, not invoice date.

Integrate properly into Magento backend admin so that the report can be run via the web interface.

## Important

I'm a programmer, not an accountant. If you want to use this extension, I strongly recommend you have the output checked by a qualified professional to ensure it meets your needs. Whilst I endeavour to ensure that the output is correct, I can take no responsibility for any errors in the reports generated.