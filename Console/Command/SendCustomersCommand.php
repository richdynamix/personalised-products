<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCustomersCommand extends AbstractCustomerCommand
{
    protected function configure()
    {
        $this->setName('pio:send:customers');
        $this->setDescription('Send all customers to the PredictionIO event server');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $customer = $this->_customerFactory->create();
        $collection = $customer->getCollection()->getAllIds();

        $customerCount = count($collection);

        $output->writeln('Preparing to send '. $customerCount .' customers');

        try {
            $sentCount = $this->_sendCustomerData($collection);
            $output->writeln('Successfully sent '. $sentCount .' customers to the PredictionIO event server');
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }

    }

    private function _sendCustomerData($collection)
    {
        $collectionCount = count($collection);
        $i = 0;
        foreach ($collection as $customerId) {
            if ($this->_eventServer->saveCustomerData($customerId)) {
                ++$i;
            }
        }

        if ($collectionCount != $i) {
            throw new Exception('There was a problem sending the customer data, check the log file for more information');
        }

        return $i;

    }
}
