<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Customer\Model\CustomerFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractCustomerCommand extends Command
{
    protected $_customerFactory;

    protected $_eventClient;

    public function __construct(CustomerFactory $customerFactory, Client $eventClient)
    {
        $this->_customerFactory = $customerFactory;
        $this->_eventClient = $eventClient;
        parent::__construct();
    }

    protected function _sendCustomerData($collection)
    {
        $collectionCount = count($collection);
        $sentCustomersCount = 0;
        foreach ($collection as $customerId) {
            if ($this->_eventClient->saveCustomerData($customerId)) {
                ++$sentCustomersCount;
            }
        }

        if ($collectionCount != $sentCustomersCount) {
            throw new Exception('There was a problem sending the customer data, check the log file for more information');
        }

        return $sentCustomersCount;

    }

    protected function _getCustomerCollection()
    {
        $customer = $this->_customerFactory->create();
        return $customer->getCollection()->getAllIds();
    }

}
