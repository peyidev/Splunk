<?php

namespace Splunk\Logging\Plugin\Checkout;

class Index
{

    protected $logger;
    protected $request;
    protected $_productRepository;
    protected $_session;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->_session = $session;
        $this->_productRepository = $productRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function beforeExecute()
    {

        $items = $this->_session->getQuote()->getAllVisibleItems();
        $qId = (int)$this->_session->getQuoteId();

        $response = array();

        foreach ($items as $item) {

            $response[] = array(
                array(
                    "sku" => $item->getSku(),
                    "qty" => (int)$item->getQty(),
                )
            );
        }

        $this->logger->debug(array(
            "action" => "checkoutVisit",
            "quote-id" => $qId,
            "cart" => $response,
        ));
    }

}