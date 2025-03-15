<?php

namespace Quickcomm\Gravity\Observer;

use Quickcomm\Gravity\Model\Config\Source\HookType;

/**
 * Class BeforeDeleteProduct
 * @package Quickcomm\Gravity\Observer
 */
class BeforeDeleteProduct extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::DELETE_PRODUCT;
}
