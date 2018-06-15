<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use InvalidArgumentException;
use Iresults\Shell\InputInterface;
use Mage;
use Mage_Sales_Model_Resource_Order_Collection as OrderCollection;

/**
 * Command to list Orders
 */
abstract class AbstractOrderCommand extends \Iresults\Gdpr\Model\Shell\Command\AbstractCommand
{
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Filter Orders with a minimum age
     *
     * @param OrderCollection $collection
     * @param int             $age
     * @return OrderCollection
     * @throws \Exception
     */
    protected function filterByMinimumAge(OrderCollection $collection, $age)
    {
        $utcTimezone = new \DateTimeZone('UTC');
        $fromDateLocal = (new \DateTimeImmutable())
            ->setTimezone($utcTimezone)
            ->modify(sprintf('-%d days', $age));

        $collection->addAttributeToFilter(
            'created_at',
            ['to' => $fromDateLocal->format(self::DEFAULT_DATETIME_FORMAT)]
        );

        return $collection;
    }

    /**
     * Filter Orders by the Customer's email
     *
     * @param OrderCollection $collection
     * @param string          $email
     * @return OrderCollection
     */
    protected function filterByEmail(OrderCollection $collection, $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Argument "email" must be a valid email address');
        }
        $collection->addFieldToFilter('customer_email', $email);

        return $collection;
    }

    /**
     * Filter Orders by the Customer's ID
     *
     * @param OrderCollection $collection
     * @param int|null        $customerId
     * @return OrderCollection
     */
    protected function filterByCustomerId(OrderCollection $collection, $customerId)
    {
        if ($customerId === null) {
            $collection->addFieldToFilter('customer_id', ['null' => '---']);
        } else {
            $collection->addFieldToFilter('customer_id', $customerId);
        }

        return $collection;
    }


    /**
     * Filter guest Orders
     *
     * @param OrderCollection $collection
     * @param bool            $showGuests
     * @return OrderCollection
     */
    protected function filterByGuest(OrderCollection $collection, $showGuests)
    {
        if ($showGuests) {
            $collection->addFieldToFilter('customer_is_guest', ['eq' => 1]);
        } else {
            $collection->addFieldToFilter('customer_is_guest', ['eq' => 0]);
        }

        return $collection;
    }


    /**
     * @return OrderCollection
     */
    protected function getOrdersCollection()
    {
        return Mage::getModel('sales/order')->getCollection();
    }

    /**
     * @param InputInterface  $input
     * @param OrderCollection $orders
     * @return OrderCollection
     * @throws \Exception
     */
    protected function applyMinimumAgeFilter(InputInterface $input, OrderCollection $orders)
    {
        if (!$input->hasArgument('age')) {
            return $orders;
        }

        $age = $input->getArgument('age');
        if (!is_numeric($age)) {
            throw new InvalidArgumentException('Argument "age" must be numeric');
        }

        return $this->filterByMinimumAge($orders, (int)$age);
    }

    /**
     * @param InputInterface  $input
     * @param OrderCollection $orders
     * @return OrderCollection
     * @throws \Exception
     */
    protected function applyCustomerFilter(InputInterface $input, OrderCollection $orders)
    {
        if (!$input->hasArgument('customer')) {
            return $orders;
        }

        $customer = $input->getArgument('customer');
        if (is_numeric($customer)) {
            return $this->filterByCustomerId($orders, (int)$customer);
        }
        if (filter_var($customer, FILTER_VALIDATE_EMAIL)) {
            return $this->filterByEmail($orders, $customer);
        }

        if (is_bool($customer)) {
            $description = '(bool) ' . ($customer ? 'True' : 'False');
        } else {
            $description = '(' . gettype($customer) . ')';
        }
        throw new \InvalidArgumentException(sprintf('Unsupported customer filter value: %s', $description));
    }

    /**
     * @param InputInterface  $input
     * @param OrderCollection $orders
     * @return OrderCollection
     * @throws \Exception
     */
    protected function applyGuestFilter(InputInterface $input, OrderCollection $orders)
    {
        if ($input->hasArgument('guests') || $input->hasArgument('guest')) {
            return $this->filterByGuest($orders, true);
        }

        return $orders;
    }

    /**
     * @param InputInterface  $input
     * @param OrderCollection $orders
     * @return OrderCollection
     * @throws \Exception
     */
    protected function applyNoGuestFilter(InputInterface $input, OrderCollection $orders)
    {
        if ($input->hasArgument('no-guests') || $input->hasArgument('no-guest')) {
            return $this->filterByGuest($orders, false);
        }

        return $orders;
    }

    /**
     * @param InputInterface  $input
     * @param OrderCollection $orders
     * @return OrderCollection
     */
    protected function applyEmailFilter(InputInterface $input, OrderCollection $orders)
    {
        if (!$input->hasArgument('email')) {
            return $orders;
        }

        $email = $input->getArgument('email');
        if ('' === trim($email)) {
            throw new InvalidArgumentException('Argument "email" must not be empty');
        }

        return $this->filterByEmail($orders, $email);
    }
}
