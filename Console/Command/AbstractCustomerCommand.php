<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Customer\Model\CustomerFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class AbstractCustomerCommand
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
abstract class AbstractCustomerCommand extends Command
{
    /**
     * @var CustomerFactory
     */
    private $_customerFactory;

    /**
     * @var Client
     */
    private $_eventClient;

    /**
     * AbstractCustomerCommand constructor.
     * @param CustomerFactory $customerFactory
     * @param Client $eventClient
     */
    public function __construct(CustomerFactory $customerFactory, Client $eventClient)
    {
        $this->_customerFactory = $customerFactory;
        $this->_eventClient = $eventClient;
        parent::__construct();
    }

    /**
     * Send the customer data to PredictionIO using the event client
     *
     * @param $collection
     * @return int
     */
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

    /**
     * Get a collection of all customer ID's, regardless if they have been exported before.
     *
     * @return array
     */
    protected function _getCustomerCollection()
    {
        $customer = $this->_customerFactory->create();
        return $customer->getCollection()->getAllIds();
    }
}
