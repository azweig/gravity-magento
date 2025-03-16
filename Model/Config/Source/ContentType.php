<?php
namespace Gravity\Model\Config\Source;

use Gravity\Model\Config\AbstractSource;

/**
 * Class ContentType
 * @package Gravity\Model\Config\Source
 */
class ContentType extends AbstractSource
{
    const APPLICATION_JSON = 'application/json';
    const APPLICATION_X_WWW_FORM_URLENCODE = 'application/x-www-form-urlencoded';
    const APPLICATION_XML = 'application/xml';
    const APPLICATION_JSON_CHARSET_UTF_8 = 'application/json; charset=UTF-8';

    /**
     * Get options in "key-value" format
     *
     * @return array
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

