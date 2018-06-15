<?php

namespace Iresults\Gdpr\Model\Shell\Command\Sales\Order;

use InvalidArgumentException;
use Iresults\Shell\Color;
use Iresults\Shell\InputInterface;
use Iresults\Shell\OutputInterface;
use Mage;
use Mage_Sales_Model_Order as Order;
use Mage_Sales_Model_Quote as Quote;

/**
 * Command to show information about an Order
 */
class ShowCommand extends AbstractOrderCommand
{
    public function execute(InputInterface $input, OutputInterface $output, OutputInterface $errorOutput)
    {
        $id = $this->getRequestedId($input);
        $order = $this->fetchOrder($id);
        if (!$order) {
            $errorOutput->writeln(Color::red('Order #%s not found', $id));

            return;
        }

        $this->printHeader('Order', $output);
        $this->printOrder($order, $output, true);

        if ($input->hasArgument('verbose') || $input->hasArgument('v')) {
            $quote = $this->fetchQuote($order->getQuoteId());
            if ($quote) {
                $this->printQuote($quote, $output);
            } else {
                $errorOutput->writeln(Color::red('Quote #%s not found', $order->getQuoteId()));
            }
        }
    }

    public static function getName()
    {
        return 'sales:order:show';
    }

    /**
     * @param string|int $id
     * @return Order|null Return the Order or NULL if it could not be found
     */
    protected function fetchOrder($id)
    {
        /** @var Order $order */
        $order = Mage::getModel('sales/order');

        if ($id >= 100000000) {
            $order->loadByIncrementId($id);
        } else {
            $order->load($id);
        }

        if (!$order->getId()) {
            return null;
        }

        return $order;
    }

    /**
     * @param int $id
     * @return Quote|null Return the Quote or NULL if it could not be found
     */
    protected function fetchQuote($id)
    {
        /** @var Quote $quote */
        $quote = Mage::getModel('sales/quote');
        $quote->loadByIdWithoutStore($id);
        if (!$quote->getId()) {
            return null;
        }

        return $quote;
    }

    /**
     * @param InputInterface $input
     * @return int|string|float
     */
    protected function getRequestedId(InputInterface $input)
    {
        if (!$input->hasArgument('id')) {
            throw new InvalidArgumentException('Missing argument "id"');
        }
        $id = $input->getArgument('id');
        if (!is_numeric($id)) {
            throw new InvalidArgumentException('Argument "id" must be numeric');
        }

        return $id;
    }
}
