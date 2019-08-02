<?php

namespace Splunk\Logging\Plugin\Checkout;

class Add
{

    protected $logger;
    protected $request;
    protected $_productRepository;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->_productRepository = $productRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function afterExecute()
    {
        $params = $this->request->getPostValue();
        $id = $sku = $qty = -1;
        if(!empty($params['product']) && !empty($params['qty'])){
            $product = $this->_productRepository->getById($params['product']);
            $id = (int)$product->getId();
            $sku = $product->getSku();
            $qty = (int)$params['qty'];
        }

        $this->logger->info(array(
            "action" => "AddToCart",
            "productId" => $id,
            "sku" => $sku,
            "qty" => $qty,
        ));


    }

}