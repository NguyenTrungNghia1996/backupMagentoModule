<?php


namespace Report\Order\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AdminCheckoutSubmitAllAfter implements ObserverInterface
{
    protected $backendAuthSession;
    protected $logger;
    public function __construct(\Magento\Backend\Model\Auth\Session $backendAuthSession)
    {
        $this->backendAuthSession = $backendAuthSession;
    }

    public function execute(Observer $observer)
    {
        $adminUserName = $this->backendAuthSession->getUser()->getUserName();
        if ($adminUserName) {
            $order = $observer->getEvent()->getOrder();
            $order->setSalesRepresentative($adminUserName);
            $order->save();
        }
    }
}
