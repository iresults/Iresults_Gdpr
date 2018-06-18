<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use Iresults\Shell\Color;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Resource_Order_Collection as OrderCollection;

/**
 * Command to list Orders
 */
class ListCommand extends AbstractOrderCommand
{
    /**
     * Return the available filter arguments
     *
     * At least one of these arguments must be provided when invoking the CLI tool
     *
     * @return string[]
     */
    protected function getAvailableFilters()
    {
        return [
            'age',
            'customer',
            'guests',
            'guest',
            'no-guests',
            'no-guest',
            'email',
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        $orders = $this->getMatchingOrders($input, $errorOutput);

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
    protected function getMatchingOrders(InputInterface $input, OutputInterface $errorOutput)
    {
        if (!$this->hasFilterApplied($input)) {
            $errorOutput->writeln(Color::red('[ERROR] No filter argument given'));

            $filterArguments = PHP_EOL
                . implode(
                    PHP_EOL,
                    array_map(
                        function ($f) {
                            return ' - ' . $f;
                        },
                        $this->getAvailableFilters()
                    )
                );
            $errorOutput->writeln('Provide at least one of the following arguments: %s', $filterArguments);
            exit(1);
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

    /**
     * @param InputInterface $input
     * @return bool
     */
    protected function hasFilterApplied(InputInterface $input)
    {
        if (0 === count($input->getArguments())) {
            return false;
        }

        $availableFilters = $this->getAvailableFilters();
        foreach ($availableFilters as $argumentName) {
            if ($input->hasArgument($argumentName)) {
                return true;
            }
        }

        return false;
    }
}
