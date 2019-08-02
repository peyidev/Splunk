<?php

namespace Splunk\Logging\Plugin\Checkout;

class Payment
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

    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $payment,
        callable $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {

        $qId = (int)$this->_session->getQuoteId();
        $items = $this->_session->getQuote()->getAllVisibleItems();

        $cart = array();

        foreach ($items as $item) {

            $cart[] = array(
                array(
                    "sku" => $item->getSku(),
                    "qty" => (int)$item->getQty(),
                )
            );
        }


        $result = $proceed($cartId, $paymentMethod, $billingAddress);

        $this->logger->debug(
            array(
                "action" => "checkoutPayment",
                "quote-id" => $qId,
                "order-id" => !empty($result) ? $result : null,
                "paymentMethod" => $paymentMethod->getMethod(),
                "cart" => $cart,
                "total" => !empty($result) ? $this->_session->getQuote()->getGrandTotal() : 0,
                "success" => !empty($result)
            )
        );

        return $result;
    }

}