<?php 
namespace App\Http\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class FibApi
{
    protected string $apiUrl;
    protected string $apiClientId;
    protected string $apiClientSecret;
    protected ?string $token = null;

    public function __construct() {
        $this->apiUrl = env('FIB_API_URL');
        $this->apiClientId = env('FIB_API_CLIENT_ID');
        $this->apiClientSecret = env('FIB_API_CLIEN_SECRET');
        // if (session()->has('fibApiToken')) {
        //     $this->token = session('fibApiToken');
        // } else {
        //     $this->connect();
        // }
        $this->connect();
    }
    
    public function connect(){
        $path = 'auth/realms/fib-online-shop/protocol/openid-connect/token';

        $response = Http::withHeaders($this->getHeaders())->asForm()->post("$this->apiUrl/$path", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiClientId,
            'client_secret' => $this->apiClientSecret
        ]);
        $data = $response->json();
        
        $this->token = $data['access_token'];
        session(['fibApiToken' => $data['access_token']]);
    }

    protected function getHeaders() {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        if ($this->token !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }
        return $headers;
    }

    public function createPayment($amount, $currency = "IQD") {
        $path = 'protected/v1/payments';

        if ($this->token === null) {
            $this->connect();
        }
        $callback = route('transaction.update');
        $body = json_encode([
            'monetaryValue' => [
                'amount' => $amount,
                'currency' => $currency
            ],
            'statusCallbackUrl' => $callback
        ]);
        $response = Http::withHeaders($this->getHeaders())->withBody($body)->post("$this->apiUrl/$path");
        
        return $this->response($response);

    }

    public function checkPaymentStatus($paymentId) {
        $path = "protected/v1/payments/$paymentId/status";

        if ($this->token === null) {
            $this->connect();
        }
        
        $response = Http::withHeaders($this->getHeaders())->get("$this->apiUrl/$path");
            
        return $this->response($response);

    }

    public function refundPayment($paymentId) {
        $path = "protected/v1/payments/$paymentId/refund";

        if ($this->token === null) {
            $this->connect();
        }
      
        $response = Http::withHeaders($this->getHeaders())->post("$this->apiUrl/$path", []);

        return $this->response($response);
    }

    public function cancelPayment($paymentId) {
        $path = "protected/v1/payments/$paymentId/cancel";

        if ($this->token === null) {
            $this->connect();
        }
        
        $response = Http::withHeaders($this->getHeaders())->post("$this->apiUrl/$path", []);

        return $this->response($response);
    }



    public function response($resp) {
        if ($resp->successful()) {
            return [
                'code' => 200,
                'data' => $resp->json()
            ];
        } else {
            return [
                'code' => 400,
                'data' => $resp->json() 
            ];
        }
    }
    

}
