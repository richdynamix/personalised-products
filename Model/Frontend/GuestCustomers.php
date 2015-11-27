<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;
use \Richdynamix\PersonalisedProducts\Helper\Config;

class GuestCustomers
{

    public function __construct(
        Config $config,
        Factory $predictionIOFactory
    )
    {
        $this->_config = $config;
        $this->_predictionIOFactory = $predictionIOFactory;
    }

    public function setGuestCustomerProductView($product)
    {
        //todo add customer product views to the session to process when the customer logs in
    }

}