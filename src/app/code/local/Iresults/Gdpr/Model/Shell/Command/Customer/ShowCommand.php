<?php

namespace Iresults\Gdpr\Model\Shell\Command\Customer;

use InvalidArgumentException;
use Iresults\Gdpr\Model\Shell\Command\AbstractCommand;
use Iresults\Shell\Color;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Mage;
use Mage_Customer_Model_Customer as Customer;
use Mage_Sales_Model_Resource_Order as Order;
use Mage_Sales_Model_Resource_Order_Collection as OrderCollection;
use Mage_Sales_Model_Resource_Quote_Collection as QuoteCollection;

/**
 * Command to show information about a given customer
 */
class ShowCommand extends AbstractCommand
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

        if ($customer->getId()) {
            $output->writeln('ID:           %s', $customer->getId());
            $output->writeln('Email:        %s', $customer->getData('email'));
            $output->writeln('Full name:    %s', $customer->getName());
            $output->writeln();
            $this->printHeader('Addresses', $output);
            foreach ($customer->getAddresses() as $address) {
                $this->printAddress($address, $output);
            }
        } else {
            $errorOutput->writeln(Color::yellow('Customer with email %s not found', $email));
        }
        $output->writeln();

        $orders = $this->getOrders($email);
        if (0 < count($orders)) {
            $this->printHeader('Orders', $output);
            foreach ($orders as $order) {
                $this->printOrder($order, $output, false);
            }
            $output->writeln();
        }

        $quotes = $this->getQuotes($email);
        if (0 < count($quotes)) {
            $this->printHeader('Quotes', $output);
            foreach ($quotes as $order) {
                $this->printQuote($order, $output);
            }
        }
    }

    public static function getName()
    {
        return 'customer:show';
    }

    public function getCustomer($email, $websiteId)
    {
        /** @var Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setData('website_id', $websiteId);

        return $customer->loadByEmail($email);
    }

    /**
     * @param string $email
     * @return OrderCollection|Order
     */
    private function getOrders($email)
    {
        /** @var OrderCollection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('customer_email', $email);

        return $collection;
    }

    /**
     * @param string $email
     * @return QuoteCollection
     */
    private function getQuotes($email)
    {
        /** @var QuoteCollection $collection */

        $collection = Mage::getModel('sales/quote')->getCollection();
        $collection->addFieldToFilter('customer_email', $email);

        return $collection;
    }
}
