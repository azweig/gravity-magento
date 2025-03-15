<?php
namespace Quickcomm\Gravity\Logger\Handler;

use Monolog\Logger;
/**
 * Cuoma logger handler
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
    protected $fileName = '/var/log/quickcomm_gravity.log';

}