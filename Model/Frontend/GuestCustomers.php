<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend;

use \Magento\Framework\Session\SessionManager;

/**
 * Class GuestCustomers is used for saving customer product views to the session
 * when the customer is not logged int.
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GuestCustomers
{
    /**
     * @var SessionManager
     */
    protected $_sessionManager;

    /**
     * GuestCustomers constructor.
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->_sessionManager = $sessionManager;
    }

    /**
     * @param $productId
     */
    public function setGuestCustomerProductView($productId)
    {
        $guestProductViews = $this->_sessionManager->getGuestProductViews();
        $guestProductViews[] = $productId;

        $this->_sessionManager->setGuestProductViews($guestProductViews);
    }

}
