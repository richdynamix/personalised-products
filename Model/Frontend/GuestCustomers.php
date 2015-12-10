<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend;

use \Magento\Framework\Session\SessionManager;

/**
 * Class GuestCustomers is used for saving customer product views to the session
 * when the customer is not logged in.
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class GuestCustomers
{
    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * GuestCustomers constructor.
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Save the product ids to the session for each product the guest has viewed
     *
     * @param $productId
     */
    public function setGuestCustomerProductView($productId)
    {
        $guestProductViews = $this->sessionManager->getGuestProductViews();
        $guestProductViews[] = $productId;

        $this->sessionManager->setGuestProductViews($guestProductViews);
    }

}
