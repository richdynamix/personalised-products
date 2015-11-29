<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Richdynamix\PersonalisedProducts\Console\Command\AbstractOrdersCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Config\Definition\Exception\Exception;

class SendOrdersCommand extends AbstractOrdersCommand
{
    protected function configure()
    {
        $this->setName('pio:send:orders');
        $this->setDescription('Send all customer-buy-products actions to the PredictionIO event server');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $customerProducts = $this->_getCustomerProductCollection($this->_getOrderCollection());
        $output->writeln('Preparing to send '. count($customerProducts) .' orders');
        $output->writeln(
            'Preparing ' . $this->_getCustomerCount() . ' customers with a total of ' .
            $this->_getProductCount() . ' products'
        );

        try {
            $sentCount = $this->_sendCustomerBuyProductData($customerProducts);
            $output->writeln(
                'Successfully set a total of ' . $this->_getProductCount()
                . ' product purchases to ' . $this->_getCustomerCount() . ' customers' .
                ' on a total of ' . $sentCount . ' orders'
            );
        } catch (Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
