<?php
/**
 * Gravity HTTP method source model
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Model\Config\Source;

/**
 * HTTP method source model
 */
class Method extends AbstractSource
{
    /**
     * HTTP method constants
     */
    const GET = 'GET';
    const HEAD = 'HEAD';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';
    const TRACE = 'TRACE';
    const PATCH = 'PATCH';

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            '' => __('--Please Select--'),
            self::GET => 'GET',
            self::HEAD => 'HEAD',
            self::POST => 'POST',
            self::PUT => 'PUT',
            self::DELETE => 'DELETE',
            self::CONNECT => 'CONNECT',
            self::OPTIONS => 'OPTIONS',
            self::TRACE => 'TRACE',
            self::PATCH => 'PATCH',
        ];
    }
}

