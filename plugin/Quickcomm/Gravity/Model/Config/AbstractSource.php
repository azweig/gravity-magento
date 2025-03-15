<?php
namespace Quickcomm\Gravity\Model\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class AbstractSource
 * @package Quickcomm\Gravity\Model\Config
 */
abstract class AbstractSource implements ArrayInterface
{
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
    abstract public function toArray();
}
