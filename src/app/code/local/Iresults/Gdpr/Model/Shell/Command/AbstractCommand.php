<?php

namespace Iresults\Gdpr\Model\Shell\Command;

use Iresults\Shell\OutputInterface;
use Mage_Customer_Model_Address_Abstract as Address;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Quote as Quote;

abstract class AbstractCommand extends \Iresults\Shell\Command\AbstractCommand
{
    /**
     * Output the given header
     *
     * @param string          $header
     * @param OutputInterface $output
     */
    protected function printHeader($header, OutputInterface $output)
    {
        $output->writeln(str_pad(' ' . strtoupper($header) . ' ', 46, '=', STR_PAD_BOTH));
    }

    /**
     * Output information about the given Address
     *
     * @param Address         $address
     * @param OutputInterface $output
     */
    protected function printAddress(Address $address, OutputInterface $output)
    {
        $output->writeln('Active        %s', $this->fmtBool($address->getData("is_active")));
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

    /**
     * Output information about the given Order
     *
     * @param Order           $order
     * @param OutputInterface $output
     * @param bool            $withCustomer
     */
    protected function printOrder(Order $order, OutputInterface $output, $withCustomer)
    {
        if (!$order->getId()) {
            $output->writeln('Order not found');
            $output->writeln();

            return;
        }
        $output->writeln('ID:           %s', $order->getRealOrderId());
        if ($withCustomer) {
            $output->writeln(
                'Customer:     %s (%s | %s)',
                $order->getCustomerName(),
                $order->getCustomerEmail(),
                $order->getCustomerIsGuest() ? 'Guest' : 'Registered'
            );
        }
        $output->writeln('Price:        %s', $this->fmtPrice($order->getGrandTotal(), $order->getOrderCurrencyCode()));
        $output->writeln('Status:       %s', $order->getStatusLabel());
        $output->writeln('Created:      %s', $this->fmtDate($order->getCreatedAt()));
        $output->writeln('Updated:      %s', $this->fmtDate($order->getUpdatedAt()));
        $output->writeln('Deleted:      %s', $this->fmtBool($order->isDeleted()));
        $output->writeln();
    }

    /**
     * Output information about the given Quote
     *
     * @param Quote           $quote
     * @param OutputInterface $output
     */
    protected function printQuote(Quote $quote, OutputInterface $output)
    {
        if (!$quote->getId()) {
            $output->writeln('Quote not found');
            $output->writeln();

            return;
        }
        $output->writeln('ID:           %s', $quote->getId());
        $output->writeln('Price:        %s', $this->fmtPrice($quote->getGrandTotal(), $quote->getQuoteCurrencyCode()));
        $output->writeln('Created:      %s', $this->fmtDate($quote->getCreatedAt()));
        $output->writeln('Updated:      %s', $this->fmtDate($quote->getUpdatedAt()));
        $output->writeln('Deleted:      %s', $this->fmtBool($quote->isDeleted()));
        $output->writeln();
    }

    /**
     * Format the given boolean value
     *
     * @param int|string $value
     * @return string
     */
    protected function fmtBool($value)
    {
        if (is_numeric($value)) {
            $value = 1 === intval($value);
        }

        return $value ? 'True' : 'False';
    }

    /**
     * Format the given date
     *
     * @param \Zend_Date|string|true $input
     * @param string                 $format
     * @return string
     */
    protected function fmtDate($input, $format = 'r')
    {
        \Iresults\Shell\Assert::assertString($format, 'format');
        if (null === $input) {
            return '';
        }
        $date = new \DateTime('@' . \Varien_Date::toTimestamp($input));

        return $date->format($format);
    }

    /**
     * Format the given price
     *
     * @param float  $price
     * @param string $currencyCode
     * @return string
     */
    protected function fmtPrice($price, $currencyCode)
    {
        return number_format(round($price, 2), 2) . ' ' . $currencyCode;
    }
}
