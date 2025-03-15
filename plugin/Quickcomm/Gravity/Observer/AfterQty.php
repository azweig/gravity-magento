<?php
namespace Quickcomm\Gravity\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Quickcomm\Gravity\Model\Config\Source\HookType;

/**
 * Class AfterQty
 * @package Quickcomm\Gravity\Observer
 */
class AfterQty extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::UPDATE_QTY;

  
}
