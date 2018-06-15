<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use InvalidArgumentException;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Resource_Order_Collection as OrderCollection;

/**
 * Command to list Orders
 */
class ListCommand extends AbstractOrderCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        $orders = $this->getMatchingOrders($input);

        $this->printHeader('Orders', $output);
        foreach ($orders as $order) {
            $this->printOrder($order, $output, true);
        }
        $output->writeln('Found %d orders', count($orders));
    }

    public static function getName()
    {
        return 'sales:order:list';
    }

    /**
     * @param InputInterface $input
     * @return Order[]|OrderCollection
     * @throws \Exception
     */
    protected function getMatchingOrders(InputInterface $input)
    {
        if (0 === count($input->getArguments())) {
            throw new InvalidArgumentException('No filter argument given');
        }
        /** @var OrderCollection|Order[] $orders */
        $orders = $this->getOrdersCollection();

        $orders = $this->applyCustomerFilter($input, $orders);
        $orders = $this->applyGuestFilter($input, $orders);
        $orders = $this->applyNoGuestFilter($input, $orders);
        $orders = $this->applyMinimumAgeFilter($input, $orders);
        $orders = $this->applyEmailFilter($input, $orders);

        return $orders;
    }
}
