<?php
namespace Gravity\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Gravity\Model\Config\Source\HookType;

/**
 * Class AfterQty
 * @package Gravity\Observer
 */
class AfterQty extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::UPDATE_QTY;
}

