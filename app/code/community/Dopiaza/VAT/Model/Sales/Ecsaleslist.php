<?php


class Dopiaza_VAT_Model_Sales_ECSalesList extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::__construct();

        // Initialise dates to today
        $this->setFrom(new \DateTime());
        $this->setTo(new \DateTime());
    }

    /**
     * Run the report
     */
    public function run()
    {
        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices */
        $invoices = $this->getSalesInvoices();
        $creditMemos = $this->getCreditMemos();
        $this->extractData($invoices, $creditMemos);
    }

    /**
     * Get the sales invoices for the period
     *
     * @return Mage_Sales_Model_Resource_Order_Invoice_Collection
     */
    protected function getSalesInvoices()
    {
        $from = $this->getFrom()->format('Y-m-d');
        $to = $this->getTo()->add(new \DateInterval('P1D'))->format('Y-m-d');

        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices */
        $invoices = \Mage::getModel("sales/order_invoice")
            ->getCollection()
            ->join(array(
                'shipping_address'=> 'order_address'),
                'shipping_address.entity_id = main_table.shipping_address_id',
                array('shipping_address.vat_id', 'shipping_address.vat_is_valid'))
            ->addAttributeToFilter('created_at', array('gteq' => $from))
            ->addAttributeToFilter('created_at', array('lt' => $to))
            ->addAttributeToFilter('shipping_address.vat_id', array('notnull' => true))
            ->addAttributeToFilter('shipping_address.vat_is_valid', 1)
            ;

        return $invoices;
    }

    /**
     * Get the credit memos for the period
     *
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    protected function getCreditMemos()
    {
        $from = $this->getFrom()->format('Y-m-d');
        $to = $this->getTo()->add(new \DateInterval('P1D'))->format('Y-m-d');

        /** @var Mage_Sales_Model_Resource_Order_Creditmemo_Collection $creditMemos */
        $creditMemos = \Mage::getModel("sales/order_creditmemo")
            ->getCollection()
            ->join(array(
                'shipping_address'=> 'order_address'),
                'shipping_address.entity_id = main_table.shipping_address_id',
                array('shipping_address.vat_id', 'shipping_address.vat_is_valid'))
            ->addAttributeToFilter('created_at', array('gteq' => $from))
            ->addAttributeToFilter('created_at', array('lt' => $to))
            ->addAttributeToFilter('shipping_address.vat_id', array('notnull' => true))
            ->addAttributeToFilter('shipping_address.vat_is_valid', 1)
        ;

        return $creditMemos;
    }

    /**
     * Extract the VAT data from invoices and credit memos for the report
     *
     * @param Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices
     * @param Mage_Sales_Model_Resource_Order_Creditmemo_Collection $creditMemos
     */
    protected function extractData(Mage_Sales_Model_Resource_Order_Invoice_Collection $invoices,
                                   Mage_Sales_Model_Resource_Order_Creditmemo_Collection $creditMemos)
    {
        $data = array();

        foreach ($invoices as $invoice)
        {
            $vatId = $invoice->getVatId();
            $amount = $invoice->getGrandTotal();

            if (!array_key_exists($vatId, $data))
            {
                $data[$vatId] = 0.0;
            }

            $data[$vatId] += $amount;
        }

        foreach ($creditMemos as $creditMemo)
        {
            $vatId = $creditMemo->getVatId();
            $amount = $creditMemo->getGrandTotal();

            if (!array_key_exists($vatId, $data))
            {
                $data[$vatId] = 0.0;
            }

            $data[$vatId] -= $amount;
        }

        $this->setAmounts($data);
    }
}