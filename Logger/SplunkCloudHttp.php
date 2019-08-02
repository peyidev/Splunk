<?php

namespace Splunk\Logging\Logger;


class SplunkCloudHttp extends SplunkAbstract
{

    protected $_curl;
    protected $url = '';
    protected $token = '';

    function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl
    )
    {
        $this->_curl = $curl;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $url = $scopeConfig->getValue('splunk_config/general/splunkcloud_url', 'store');
        $this->token = trim($scopeConfig->getValue('splunk_config/general/splunkhttp_token', 'store'));
        $this->url = $this->setCloudUrl($url);

        $this->setCurlObj();

    }

    public function setCloudUrl($url){


        if(!strpos($url, "https://input-")){
            $url = str_replace('https://', 'https://input-', $url);
        }

        if(!strpos($url, ".com:8088")){
            $url = trim($url,'/ ');
            $url = str_replace('.com', '.com:8088/services/collector', $url);
        }

        return $url;
    }


    public function setCurlObj()
    {
        $this->_curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $this->_curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->_curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->_curl->setHeaders([
            'Content-Type' => 'application/json',
            "Authorization" => "Splunk " . $this->token
        ]);

    }

    public function forwardData(Array $data)
    {

        if(!empty($this->token) && !empty($this->url))
            $this->_curl->post($this->url, json_encode(array("event" => $data)));
    }

}