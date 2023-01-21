<?php

/**
 * Name: WHMCS Dreamscape Balance Widget
 * Description: This widget provides you with your Dreamscape balance on your WHMCS admin dashboard.
 * Version 1.0
 * Created by Host Media Ltd
 * Website: https://www.hostmedia.co.uk/
 */

add_hook('AdminHomeWidgets', 1, function() {
    return new dreamscapeBalanceWidget();
});

class dreamscapeBalanceWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Dreamscape Balance';
    protected $description = 'Widget provides you with your Dreamscape (reseller.ds.network) balance on your admin dashboard. Created by Host Media.';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = true;
    protected $cacheExpiry = 120;

    public function getData()
    {
      // Config
      $reseller_id = '[ResellerID]';
    	$api_key = '[APIKey]';

      // API URL
      // Live
      $soap_location = 'https://soap.secureapi.com.au/API-2.0';
      $wsdl_location = 'https://soap.secureapi.com.au/wsdl/API-2.0.wsdl';

      // Test
      //$soap_location = 'https://soap-test.secureapi.com.au/API-2.0';
      //$wsdl_location = 'https://soap-test.secureapi.com.au/wsdl/API-2.0.wsdl';

      // Set the login headers
      $authenticate = array();
      $authenticate['AuthenticateRequest'] = array();
      $authenticate['AuthenticateRequest']['ResellerID'] = $reseller_id;
      $authenticate['AuthenticateRequest']['APIKey'] = $api_key;

      // Convert $authenticate to a soap variable
      $authenticate['AuthenticateRequest'] = new SoapVar($authenticate['AuthenticateRequest'], SOAP_ENC_OBJECT);
      $authenticate = new SoapVar($authenticate, SOAP_ENC_OBJECT);
      $header = new SoapHeader($soap_location, 'Authenticate', $authenticate, false);
      $this->reseller_api_soap_client = new SoapClient($wsdl_location, array('soap_version' => SOAP_1_2, 'cache_wsdl' => WSDL_CACHE_NONE));
      $this->reseller_api_soap_client->__setSoapHeaders(array($header));

      $prepared_data = array();
      $response = $this->reseller_api_soap_client->__soapCall('GetBalance', $prepared_data);

      $dataArray = array(
        'dreamscape' => $response->APIResponse
        , 'balance' => $response->APIResponse->Balance
      );

      return $dataArray;
    }

    public function generateOutput($data)
    {

        if (isset($data['dreamscape']->Errors)) {

return <<<EOF
    <div class="widget-content-padded">
        <strong>There was an error:</strong><br/>
        {$data['dreamscape']->Errors[0]->Message}
    </div>
EOF;
        }

        return <<<EOF
    <div class="widget-content-padded">
        <div class="row text-center">
            <div class="col-sm-12">
                <h4><strong>{$data['balance']}</strong></h4>
                Balance
            </div>
        </div>
        <div class="row text-center" style="margin-top: 20px;">
            <a href="https://reseller.ds.network/reseller_add_credit/" class="btn btn-default btn-sm" target="_blank"><i class="fas fa-credit-card fa-fw"></i> Refill Account</a>
        </div>
    </div>
EOF;
    }
}
