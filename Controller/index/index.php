<?php

namespace Richdynamix\PersonalisedProducts\Controller\Index;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;
use \Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Richdynamix\PersonalisedProducts\Helper\Urls;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    protected $_predictionIOFactory;

    protected $_urls;

    protected $_config;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Factory $predictionIOFactory,
        Config $config,
        Urls $urls
    )
    {
        $this->_predictionIOFactory = $predictionIOFactory;
        $this->_config = $config;
        $this->_urls = $urls;
        parent::__construct($context);
    }

    public function execute()
    {
        $engine = $this->_predictionIOFactory->create('engine');

        $response = $engine->sendQuery(array('user'=> 'i1', 'num'=> 4));
        print_r($response);

    }
}