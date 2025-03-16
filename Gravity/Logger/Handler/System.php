<?php
/**
 * Gravity logger handler
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * System log handler
 */
class System extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * @var string
     */
    protected $fileName = '/var/log/gravity.log';
}

