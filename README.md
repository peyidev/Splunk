Magento 2 Splunk Plugin
A plugin to setup Splunk with magento and forward all logs and some enhanced functionalities as Categories and Product view and checkout flow.

Installation
Add the repository to the repositories section of your composer.json file:
"repositories": [
    {
     "type": "vcs",
     "url": "git@github.com:peyidev/Splunk.git"
    }
],
Require the module & install
composer require splunk/logging:dev-master
