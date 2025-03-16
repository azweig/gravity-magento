<?php
namespace Gravity\Logger\Handler;

use Monolog\Logger;
/**
 * Gravity logger handler
 */
class System
    extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/gravity.log';

}

