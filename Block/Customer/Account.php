<?php
/**
 * Softnoesis
 * Copyright(C) 11/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CustomerInfo
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CustomerInfo\Block\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Account extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    protected $_filesystem;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;
    protected $cacheTypeList;
    protected $cacheFrontendPool;


    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        SessionFactory $customerSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        array $data = []
    ) {
        $this->urlBuilder            = $urlBuilder;
        $this->customerSession       = $customerSession->create();
        $this->storeManager          = $storeManager;
        $this->_filesystem           = $filesystem;
        $this->customerModel         = $customerModel;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;

        parent::__construct($context, $data);

        $collection = $this->getContracts();
        $this->setCollection($collection);
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
    public function getCurrentCustomer()
    {
        return $customer = $this->customerSession->getCustomer();
    }
    public function getMediaUrl()
    {
        //return $this->getBaseUrl() . 'pub/media/';
        //$mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $mediapath = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediapath;
    }

    public function getCustomerLogoUrl($logoPath)
    {
        return $this->getMediaUrl() . 'customer' . $logoPath;
    }

    public function getLogoUrl()
    {
        $customerData = $this->customerModel->load($this->customerSession->getId());
        $accountImage = $this->getMediaUrl().'customer'.$customerData->getData('customer_avatar');

        return $accountImage;
    }
    public function getProfileExist()
    {
        $customerData = $this->customerModel->load($this->customerSession->getId());
        $profileExist = $customerData->getData('customer_avatar');

        return $profileExist;
    }
    public function getcache()
    {
        $this->cacheTypeList->cleanType('eav');
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
