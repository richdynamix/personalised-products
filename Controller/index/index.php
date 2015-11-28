<?php

namespace Richdynamix\PersonalisedProducts\Controller\Index;

use \Magento\Framework\Session\SessionManager;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    protected $_sessionManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManager $sessionManager
    )
    {
        $this->_sessionManager = $sessionManager;
        parent::__construct($context);
    }

    public function execute()
    {

        $guestProductViews = $this->_sessionManager->getGuestProductViews();
        $guestProductViews[] = rand(1, 50);

        $this->_sessionManager->setGuestProductViews($guestProductViews);

        var_dump($guestProductViews);

    }
}