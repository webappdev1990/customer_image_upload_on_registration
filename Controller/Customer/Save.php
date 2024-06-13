<?php
/**
 * Softnoesis
 * Copyright(C) 11/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CustomerInfo
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CustomerInfo\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save extends Action
{
    protected $fileUploaderFactory;
    protected $filesystem;
    protected $customer;
    protected $customerFactory;
    protected $cacheTypeList;
    protected $cacheFrontendPool;
    protected $customerRepository;

    public function __construct(
        Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $fname = substr($post['first_name'], 0, 1);
        $lname = substr($post['last_name'], 0, 1);
        //die();

        $uploader = $this->fileUploaderFactory->create(['fileId' => 'customer_avatar']);
         
        $uploader->setAllowedExtensions(['jpg', 'png', 'doc', 'docx', 'pdf']);
         
        $uploader->setAllowRenameFiles(false);
         
        $uploader->setFilesDispersion(false);
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('customer/'.$fname.'/'.$lname);
        $result = $uploader->save($path);

        $filename = $result['file'];
        //$customerId = $post['customer_id']; // Replace with the actual customer ID
        $new_path = '/'.$fname.'/'.$lname;
        $newAvatarUrl = $new_path.'/'.$filename; // Replace with the new avatar URL

        $customer = $this->customerRepository->getById($post['customer_id']);
        $Attributevalue = $customer->getCustomAttribute('my_custom_attribute');

        if ($Attributevalue == '') {
            $customerId = $post['customer_id'];
            $customer = $this->customer->load($customerId);
            $data = $newAvatarUrl;
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('customer_avatar', $data);
            $customerResource = $this->customerFactory->create();
            $this->customerRepository->save($customerData);
        } else {
             /*Update Attribute Values code start*/
            $customerId = $post['customer_id'];
            $customer = $this->customer->load($customerId);
            $data = $newAvatarUrl;
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('customer_avatar', $data);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'customer_avatar');
            /*Update Attribute Values code End*/

        }

        $this->messageManager->addSuccess(__('Update success'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $this->cacheTypeList->cleanType('eav');
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }

        return $resultRedirect->setPath('routesname/customer/index/');
    }
}
