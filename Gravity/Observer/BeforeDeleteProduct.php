<?php
/**
 * Gravity before delete product observer
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Observer;

use Gravity\Model\Config\Source\HookType;

/**
 * Before delete product observer
 */
class BeforeDeleteProduct extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::DELETE_PRODUCT;
}

