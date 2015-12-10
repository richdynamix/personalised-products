<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Richdynamix\PersonalisedProducts\Console\Command\AbstractProductCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class SendProductsCommand
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendProductsCommand extends AbstractProductCommand
{
    /**
     * Configure the console command's name and description
     */
    protected function configure()
    {
        $this->setName('pio:send:products');
        $this->setDescription('Send all products to the PredictionIO event server');
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
        $collection = $this->_getProductCollection();
        $output->writeln('Preparing to send '. count($collection) .' products');

        try {
            $sentCount = $this->_sendProductData($collection);
            $output->writeln('Successfully sent '. $sentCount .' customers to the PredictionIO event server');
        } catch (Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
