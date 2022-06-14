# WHMCS Dreamscape Balance Widget

This widget provides you with your Dreamscape balance on your WHMCS admin dashboard. It has been created to be extremely simple, easy to change to fit your needs and shouldn't need any updates to work with the latest WHMCS (unless WHMCS change core elements of their code base).

## Installation
* Download the latest master
* Open the file /modules/widgets/DreamscapeBalance.php with your editor
* Change the configuration listed below to use your Dreamscape logins (Can be found here: https://reseller.ds.network/home/tools/):

  $reseller_id = '';

  $api_key = '';

* You can disable caching on the widget or change the length of time the cache lasts using the config at the top 
* Upload to your WHMCS install, if you are uploading just 'DreamscapeBalance.php' make sure to upload it to: /WHMCS-Directory/modules/widgets/

## Requirements
* PHP's SOAP Extension must be enabled

## WHMCS Tested Versions
* 8.1
* 8.0
* 7.10

## Links
* Authors Website: https://hostmedia.uk/
* Knowledge Base: https://hostmedia.uk/client/knowledgebase/201204257/WHMCS-Dreamscape-Balance-Widget.html
* WHMCS Marketplace: https://marketplace.whmcs.com/product/5817-dreamscape-balance-widget

## Preview
![Widget Preview](https://hostmedia.uk/client/images/kb/8_dreamscape-balance-widget.png)
