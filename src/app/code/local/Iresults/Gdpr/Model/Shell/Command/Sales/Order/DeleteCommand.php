<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use Iresults\Shell\Color;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;

/**
 * Command to list Orders
 */
class DeleteCommand extends ShowCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        parent::execute($input, $output, $errorOutput);

        $order = $this->fetchOrder($this->getRequestedId($input));
        if ($order) {
            if ($input->hasArgument('force')) {
                $order->delete();
                $output->writeln(Color::green('Deleted order'));
            } else {
                $output->writeln(Color::yellow('Would delete order (add `--force\')'));
            }
        }
    }

    public static function getName()
    {
        return 'sales:order:delete';
    }
}
