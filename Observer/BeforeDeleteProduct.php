<?php

namespace Gravity\Observer;

use Gravity\Model\Config\Source\HookType;

/**
 * Class BeforeDeleteProduct
 * @package Gravity\Observer
 */
class BeforeDeleteProduct extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::DELETE_PRODUCT;
}

