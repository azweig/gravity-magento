<?php
/**
 * Gravity content type source model
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Model\Config\Source;

/**
 * Content type source model
 */
class ContentType extends AbstractSource
{
    /**
     * Content type constants
     */
    const APPLICATION_JSON = 'application/json';
    const APPLICATION_X_WWW_FORM_URLENCODE = 'application/x-www-form-urlencoded';
    const APPLICATION_XML = 'application/xml';
    const APPLICATION_JSON_CHARSET_UTF_8 = 'application/json; charset=UTF-8';

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            '' => __('--Please Select--'),
            self::APPLICATION_JSON => 'application/json',
            self::APPLICATION_X_WWW_FORM_URLENCODE => 'application/x-www-form-urlencoded',
            self::APPLICATION_XML => 'application/xml',
            self::APPLICATION_JSON_CHARSET_UTF_8 => 'application/json; charset=UTF-8',
        ];
    }
}

