<?php
/**
 * Gravity after order observer
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Gravity\Model\Config\Source\HookType;

/**
 * After order observer
 */
class AfterOrder extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::NEW_ORDER;

    /**
     * @var string
     */
    protected $hookTypeUpdate = HookType::UPDATE_ORDER;
    
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getDataObject();
        if ($item->getQGNew()) {
            parent::execute($observer);
        } else {
            $this->updateObserver($observer);
        }
    }
}

