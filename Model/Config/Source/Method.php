<?php
namespace Gravity\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Method
 * @package Gravity\Model\Config\Source
 */
class Method implements ArrayInterface
{
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
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
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

