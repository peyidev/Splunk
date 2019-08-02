# Magento 2 Splunk Plugin

A plugin to setup Splunk with magento and forward all logs and some enhanced functionalities as Categories and Product view and checkout flow.

## Installation

1. Add the repository to the repositories section of your composer.json file:
```
"repositories": [
    {
     "type": "vcs",
     "url": "git@github.com:peyidev/Splunk.git"
    }
],
```
2. Require the module & install

```
composer require splunk/logging:dev-master
```

## Usage

Instructions for implementing Splunk in your store.
   
    1. Open a Splunk account  https://www.splunk.com/en_us/software/splunk-cloud.html
    2. Create an http event colector on your instance
    3. Use your provided credentials in the admin