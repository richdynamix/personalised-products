<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Richdynamix\PersonalisedProducts\Console\Command\AbstractOrdersCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class SendOrdersCommand
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendOrdersCommand extends AbstractOrdersCommand
{
    /**
     * Configure the console command's name and description
     */
    protected function configure()
    {
        $this->setName('pio:send:orders');
        $this->setDescription('Send all customer-buy-products actions to the PredictionIO event server');
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $customerProducts = $this->getCustomerProductCollection($this->getOrderCollection());
        $output->writeln('Preparing to send '. count($customerProducts) .' orders');
        $output->writeln(
            'Preparing ' . $this->getCustomerCount() . ' customers with a total of ' .
            $this->getProductCount() . ' products'
        );

        try {
            $sentCount = $this->sendCustomerBuyProductData($customerProducts);
            $output->writeln(
                'Successfully set a total of ' . $this->getProductCount()
                . ' product purchases to ' . $this->getCustomerCount() . ' customers' .
                ' on a total of ' . $sentCount . ' orders'
            );
        } catch (Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
