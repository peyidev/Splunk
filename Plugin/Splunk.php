<?php
namespace Splunk\Logging\Plugin;

class Splunk
{

    protected $logger;
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterAddProduct(\Magento\Catalog\Model\Product $product){

        //$this->logger->info("afterAddProduct execution");
    }


    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        //$this->logger->info("Test of logging");
        return $result;
    }

    public function beforeSendResponse(\Magento\Framework\App\Response\Http $subject){

//        $this->logger->info("Test of beforeSendResponse -------------------------------");

    }
}