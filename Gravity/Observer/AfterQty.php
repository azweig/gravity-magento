<?php
/**
 * Gravity after quantity observer
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
 * After quantity observer
 */
class AfterQty extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::UPDATE_QTY;
}

