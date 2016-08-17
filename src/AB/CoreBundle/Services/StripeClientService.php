<?php
namespace AB\CoreBundle\Services;

use AB\CoreBundle\Entity\Billet;
use Doctrine\ORM\EntityManager;

class StripeClientService
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($secretKey,EntityManager $em)
    {
        $this->em = $em;
        \Stripe\Stripe::setApiKey($secretKey);
    }

    public function createCustomer($paymentToken)
    {
        $customer = \Stripe\Customer::create(array(
            "email" => "visiteur@louvre.com",
            "source" => $paymentToken
        ));

        return $customer;
    }

    public function createInvoiceItem($amount,$description)
    {
        return \Stripe\InvoiceItem::create(array(
            "amount" => $amount,
            "currency" => "eur",
            "customer" => "visiteur@louvre.com",
            "description" => $description
        ));
    }

    public function createInvoice($payImmediately = true)
    {
        $invoice = \Stripe\Invoice::create(array(
            "customer" => "visiteur@louvre.com",
        ));

        if($payImmediately){
            $invoice->pay();
        }

        return $invoice;
    }

}