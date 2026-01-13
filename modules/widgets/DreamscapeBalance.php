<?php

/**
 * Name: WHMCS Dreamscape Balance Widget
 * Description: This widget provides you with your Dreamscape balance on your WHMCS admin dashboard.
 * Version 1.2.0
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
        // Change [ResellerID] with your Reseller ID from reseller.ds.network
        // Change [APIKey] with your API ID from reseller.ds.network
        $reseller_id = '[ResellerID]';
        $api_key = '[APIKey]';

        // Timeout configuration (in seconds)
        $connect_timeout = 5;  // Max time to establish connection
        $request_timeout = 10; // Max total time for the request

        $request_id = md5(uniqid() . microtime(true));
        $signature = md5($request_id . $api_key);
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://reseller-api.ds.network/finances/balance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Api-Request-Id: ' . $request_id,
                'Api-Signature: ' . $signature,
            ],
            CURLOPT_CONNECTTIMEOUT => $connect_timeout,
            CURLOPT_TIMEOUT => $request_timeout,
            CURLOPT_FAILONERROR => false,
        ]);

        $result = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle cURL errors (timeout, connection refused, etc.)
        if ($result === false || !empty($curl_error)) {
            return [
                'dreamscape' => (object)['error_message' => 'API unavailable: ' . ($curl_error ?: 'Connection failed')],
                'balance' => null,
                'currency' => null,
            ];
        }

        // Handle HTTP errors
        if ($http_code >= 400) {
            return [
                'dreamscape' => (object)['error_message' => 'API returned HTTP ' . $http_code],
                'balance' => null,
                'currency' => null,
            ];
        }

        $response = json_decode($result);

        // Handle JSON decode errors
        if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
            return [
                'dreamscape' => (object)['error_message' => 'Invalid API response'],
                'balance' => null,
                'currency' => null,
            ];
        }

        $dataArray = array(
        'dreamscape' => $response
        , 'balance' => $response->data->balance ?? null
        , 'currency' => $response->data->currency ?? null
        );

        return $dataArray;
    }

    public function generateOutput($data)
    {
        if (isset($data['dreamscape']->error_message)) {

return <<<EOF
    <div class="widget-content-padded text-center">
        <strong>There was an error:</strong><br/>
        {$data['dreamscape']->error_message}
    </div>
EOF;
        }

        return <<<EOF
    <div class="widget-content-padded">
        <div class="row text-center">
            <div class="col-sm-12">
                <h4><strong>{$data['balance']} {$data['currency']}</strong></h4>
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
