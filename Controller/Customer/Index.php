<?php
/**
 * Softnoesis
 * Copyright(C) 11/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CustomerInfo
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CustomerInfo\Controller\Customer;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
