<?php

namespace Splunk\Logging\Logger;

abstract class SplunkAbstract{
    public abstract function forwardData(Array $data);

}