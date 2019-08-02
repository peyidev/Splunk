<?php

namespace Splunk\Logging\Plugin\Catalog;

class ProductView
{

    protected $logger;
    protected $request;
    protected $_productRepository;
    protected $_registry;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_productRepository = $productRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function beforeSendResponse(\Magento\Framework\App\Response\Http $subject){

        $uri = $_SERVER['REQUEST_URI'];

        if(strpos($uri,".html") && !strpos($uri,"pub/")){
            $uri = explode("?",$uri)[0];
            $response = array(
                "action" => "productView",
                "url" => $uri,
            );

            $this->logger->info($response);
        }

    }


}