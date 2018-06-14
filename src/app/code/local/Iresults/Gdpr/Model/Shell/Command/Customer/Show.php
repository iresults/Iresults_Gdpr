<?php

use Iresults\Shell\Command\AbstractCommand;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Mage_Customer_Model_Address_Abstract as Address;
use Mage_Customer_Model_Customer as Customer;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Quote as Quote;

/**
 * Command to show information about a given customer
 */
class Iresults_Gdpr_Model_Shell_Command_Customer_Show extends AbstractCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        if (!$input->hasArgument('email')) {
            throw new InvalidArgumentException('Missing argument "email"');
        }
        if (!$input->hasArgument('website-id')) {
            throw new InvalidArgumentException('Missing argument "website-id"');
        }

        $email = $input->getArgument('email');
        $customer = $this->getCustomer($email, $input->getArgument('website-id'));

        $output->writeln('ID:           %s', $customer->getId());
        $output->writeln('Email:        %s', $customer->getData('email'));
        $output->writeln('Full name:    %s', $customer->getName());
        $output->writeln();
        $this->printHeader('Addresses', $output);
        foreach ($customer->getAddresses() as $address) {
            $this->printAddress($address, $output);
        }
        $output->writeln();
        $this->printHeader('Orders', $output);
        foreach ($this->getOrders($email) as $order) {
            $this->printOrder($order, $output);
        }
        $output->writeln();
        $this->printHeader('Quotes', $output);
        foreach ($this->getQuotes($email) as $order) {
            $this->printQuote($order, $output);
        }
    }

    public static function getName()
    {
        return 'customer:show';
    }

    private function printHeader($header, OutputInterface $output)
    {
        $output->writeln(str_pad(' ' . strtoupper($header) . ' ', 46, '=', STR_PAD_BOTH));
    }

    private function printAddress(Address $address, OutputInterface $output)
    {
        $output->writeln('Active        %s', $this->getActive($address));
        $output->writeln('ID:           %s', $address->getId());
        $output->writeln('Name:         %s', $address->getName());
        $output->writeln('Company       %s', $address->getData("company"));
        $output->writeln('Street:       %s', $address->getStreetFull());
        $output->writeln('City          %s', $address->getData("city"));
        $output->writeln('Postcode      %s', $address->getData("postcode"));
        $output->writeln('Country:      %s', $address->getCountry());
        $output->writeln('Region:       %s', $address->getRegion());
        $output->writeln('Telephone     %s', $address->getData("telephone"));
        $output->writeln('Created       %s', $address->getData("created_at"));
        $output->writeln('Updated       %s', $address->getData("updated_at"));
        $output->writeln();
    }

    public function getCustomer($email, $websiteId)
    {
        /** @var Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setData('website_id', $websiteId);

        return $customer->loadByEmail($email);
    }

    /**
     * @param Address $address
     * @return string
     */
    private function getActive(Address $address)
    {
        return $this->fmtBool($address->getData("is_active"));
    }

    /**
     * @param int|string $value
     * @return string
     */
    private function fmtBool($value)
    {
        return 1 === intval($value) ? 'True' : 'False';
    }

    private function getOrders($email)
    {
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->addFieldToFilter('customer_email', $email);

        return $orderCollection;
    }

    private function getQuotes($email)
    {
        $collection = Mage::getModel('sales/quote')->getCollection();
        $collection->addFieldToFilter('customer_email', $email);

        return $collection;
    }

    private function printOrder(Order $order, OutputInterface $output)
    {
        $output->writeln('ID:           %s', $order->getRealOrderId());
        $output->writeln('Price:        %s', $this->fmtPrice($order->getGrandTotal(), $order->getOrderCurrencyCode()));
        $output->writeln('Status:       %s', $order->getStatusLabel());
        $output->writeln('Created:      %s', $this->fmtDate($order->getCreatedAt()));
        $output->writeln('Updated:      %s', $this->fmtDate($order->getUpdatedAt()));
        $output->writeln('Deleted:      %s', $this->fmtBool($order->isDeleted()));
        $output->writeln();
    }

    private function printQuote(Quote $quote, OutputInterface $output)
    {
        $output->writeln('ID:           %s', $quote->getId());
        $output->writeln('Price:        %s', $this->fmtPrice($quote->getGrandTotal(), $quote->getQuoteCurrencyCode()));
        $output->writeln('Created:      %s', $this->fmtDate($quote->getCreatedAt()));
        $output->writeln('Updated:      %s', $this->fmtDate($quote->getUpdatedAt()));
        $output->writeln('Deleted:      %s', $this->fmtBool($quote->isDeleted()));
        $output->writeln();
    }

    private function fmtDate($input)
    {
        $date = new DateTime('@' . Varien_Date::toTimestamp($input));

        return $date->format('r');
    }

    private function fmtPrice($price, $currencyCode)
    {

        return number_format(round($price, 2), 2) . ' ' . $currencyCode;
    }
}
