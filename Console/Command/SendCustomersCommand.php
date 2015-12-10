<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Richdynamix\PersonalisedProducts\Console\Command\AbstractCustomerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendCustomersCommand
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendCustomersCommand extends AbstractCustomerCommand
{
    /**
     * Configure the console command's name and description
     */
    protected function configure()
    {
        $this->setName('pio:send:customers');
        $this->setDescription('Send all customers to the PredictionIO event server');
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
        $collection = $this->_getCustomerCollection();
        $output->writeln('Preparing to send '. count($collection) .' customers');

        try {
            $sentCount = $this->_sendCustomerData($collection);
            $output->writeln('Successfully sent '. $sentCount .' customers to the PredictionIO event server');
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
