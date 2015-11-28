<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Customer\Model\CustomerFactory;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServer;

abstract class AbstractCustomerCommand extends Command
{
    protected $_customerFactory;

    protected $_eventServer;

    public function __construct(CustomerFactory $customerFactory, EventServer $eventServer)
    {
        $this->_customerFactory = $customerFactory;
        $this->_eventServer = $eventServer;
        parent::__construct();
    }

}
