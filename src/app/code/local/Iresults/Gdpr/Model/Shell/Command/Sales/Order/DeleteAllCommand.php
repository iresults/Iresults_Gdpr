<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use Iresults\Shell\Color;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;

/**
 * Command to list Orders
 */
class DeleteAllCommand extends ListCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        $orders = $this->getMatchingOrders($input, $errorOutput);

        // The argument `force` is required to really delete the Orders
        $force = $input->hasArgument('force');

        $this->printHeader('Orders', $output);
        foreach ($orders as $order) {
            $output->writeln(
                'Delete Order #%s | Customer: %s (%s | %s)',
                $order->getRealOrderId(),
                $order->getCustomerName(),
                $order->getCustomerEmail(),
                $order->getCustomerIsGuest() ? 'Guest' : 'Registered'
            );

            if ($force) {
                $order->delete();
            }
        }
        if ($force) {
            $output->writeln(Color::green('Deleted %d orders', count($orders)));
        } else {
            $output->writeln(Color::yellow('Would delete %d orders (add `--force\')', count($orders)));
        }
    }

    public static function getName()
    {
        return 'sales:order:delete-all';
    }
}
