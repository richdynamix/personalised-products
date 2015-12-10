<?php
namespace Richdynamix\PersonalisedProducts\Logger;

use \Monolog\Logger;
use \Magento\Framework\Logger\Handler\Base;

/**
 * Custom logging handler for saving to custom file
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Handler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name of custom file
     *
     * @var string
     */
    protected $fileName = '/var/log/personalised_products.log';
}
