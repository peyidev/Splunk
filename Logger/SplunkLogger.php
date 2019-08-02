<?php

namespace Splunk\Logging\Logger;

class SplunkLogger extends \Magento\Framework\Logger\Monolog

{
    const BACKTRACE_LEVEL = 3;

    const CALLING_CLASS = 'class';

    const CALLING_FUNCTION = 'function';

    private $envConfig;
    private $_objectManager;
    private $splunkId;


    public function __construct($name, array $handlers = [], array $processors = [])
    {


        $this->getSplunkId();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if (empty($this->envConfig)) {
            $this->envConfig = array();
        }

        // default timezone is set to 'UTC' in /app/bootstrap => date_default_timezone_set('UTC');
        // setting it to PDT here for now
        date_default_timezone_set('America/Los_Angeles');
        $handlers = array_values($handlers);
        parent::__construct($name, $handlers, $processors);
        //not needed for now if the log goes inside the docroot
        #$this->pushHandler(new \Monolog\Handler\StreamHandler('/home/log/magento.server.log', Logger::DEBUG));
    }

    private function getSplunkId()
    {
        if (empty($_COOKIE['spk'])) {
            $this->splunkId = 'SPLUNKID-' . uniqid();
            setcookie('spk' , $this->splunkId, time() + (60 * 60 * 24 * 7 * 4 * 6));
        } else {
            $this->splunkId = $_COOKIE['spk'];
        }

        return $this->splunkId;
    }

    public function addRecord($level, $message, array $context = [])
    {

        if ($message instanceof \Exception && !isset($context['exception'])) {
            $context['exception'] = $message;
        }

        $message = $message instanceof \Exception ? $message->getMessage() : $message;
        $logObj = $this->addloggingInfo($message);


        $forwarder = $this->_objectManager->create("Splunk\Logging\Logger\SplunkCloudHttp");

        $forwarder->forwardData($logObj);

        return parent::addRecord($level, json_encode($logObj), $context);
    }

    private function addLoggingInfo($message = "")
    {

        //TODO REFER URL

        //Calling class and function

        $phpClassInfo = debug_backtrace()[self::BACKTRACE_LEVEL][self::CALLING_CLASS];

        $phpClassMethodInfo = '[' . $phpClassInfo . ']' . '[' . debug_backtrace()[self::BACKTRACE_LEVEL][self::CALLING_FUNCTION] . ']';

        //Host Information
        $hostInfo = gethostname();

        //Browser Info
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $browserInfo = 'unrecognized/unknown version';
        } else {
            $browserInfo = $_SERVER['HTTP_USER_AGENT'];
        }

        //Request Info
        if (!empty($_SERVER['HTTP_HOST']) and !empty($_SERVER['REQUEST_URI'])) {
            $requestInfo = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        } else {
            $requestInfo = 'Unknown request ';
        }

        $customerInfo = array();

        $ssid = '';
        //Session Information
        if (!empty($_SESSION)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create("Magento\Customer\Model\Session");
            $ssid = !empty($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '';
        }

        if (!empty($customerSession) && $customerSession->isLoggedIn()) {
            $customerInfo['id'] = $customerSession->getCustomerId();
            $customerInfo['username'] = $customerSession->getCustomer()->getEmail();
        } else {
            $customerInfo['id'] = -1;
        };

        $objDateTime = new \DateTime('NOW');

        return array(
            "splunkId" => $this->getSplunkId(),
            "classMethodInfo" => $phpClassMethodInfo,
            "hostInfo" => $hostInfo,
            "requestInfo" => $requestInfo,
            "userAgent" => $browserInfo,
            "message" => $message,
            "customerInfo" => $customerInfo,
            "ssid" => $ssid,
            "timestamp" => $objDateTime->format('c'),
        );
    }

}

