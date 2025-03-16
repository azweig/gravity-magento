<?php
/**
 * Gravity status source model
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Model\Config\Source;

/**
 * Status source model
 */
class Status extends AbstractSource
{
    /**
     * Status constants
     */
    const SUCCESS = 1;
    const ERROR = 0;

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            self::SUCCESS => 'Success',
            self::ERROR => 'Error',
        ];
    }
}

