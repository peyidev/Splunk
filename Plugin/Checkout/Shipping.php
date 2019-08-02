<?php

namespace Splunk\Logging\Plugin\Checkout;

class Shipping
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


    public function afterSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $shipping,
        $result
    )
    {
        $address = $this->_session->getQuote()->getShippingAddress();
        $qId = (int)$this->_session->getQuoteId();

        $this->logger->debug(array(
            "action" => "checkoutShipping",
            "quote-id" => $qId,
            "address" => array(
                "id" => $address->getCustomerId(),
                "email" => $address->getEmail(),
                "state" => $address->getRegionCode(),
                "zip" => $address->getPostcode(),
            ),
        ));

        return $result;
    }

}