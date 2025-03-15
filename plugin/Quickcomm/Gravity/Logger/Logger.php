<?php
namespace Quickcomm\Gravity\Logger;
/**
 * Quickcomm\Gravity custom logger allows name changing to differentiate log call origin
 * Class Logger
 *
 * @package Quickcomm\Gravity\Logger
 */
class Logger
    extends \Monolog\Logger
{

    /**
     * Set logger name
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}