<?php

namespace Quickcomm\Gravity\Model\Config\Source;

use Quickcomm\Gravity\Model\Config\AbstractSource;

/**
 * Class Status
 * @package Quickcomm\Gravity\Model\Config\Source
 */
class Status extends AbstractSource
{
    const SUCCESS = 1;
    const ERROR = 0;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::SUCCESS => 'Success',
            self::ERROR => 'Error',
        ];
    }
}
