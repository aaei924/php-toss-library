<?php
class TossPayments {
    public function __construct($secret, $method=null) {
        $this->secretKey = $secret;
        $this->basicToken = 'Basic '.base64_encode($this->secretKey.':');
        $this->method = $method;
    }
    
    public static function curl_post($url, $header, $content) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        $rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['resCode' => $rescode, 'resData' => json_decode($response,true)];
    }
    
    public static function curl_get($url, $header, $content) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['resCode' => $rescode, 'resData' => json_decode($response,true)];
    }
    
    public function approvePayment() {
        return self::curl_post('https://api.tosspayments.com/v1/payments/'.$this->paymentKey,
        ['Authorization: '.$this->basicToken,
        'Content-Type: application/json'],
        json_encode(['orderId' => $this->orderId,'amount' => $this->amount]));
    }
    
    public function cancelPayment() {
        return self::curl_post('https://api.tosspayments.com/v1/payments/'.$this->paymentKey.'/cancel',
        ['Authorization: '.$this->basicToken,
        'Content-Type: application/json'],
        json_encode(['cancelReason' => $this->reason,'cancelAmount' => $this->amount, 'refundReceiveAccount' => $this->refundAccount]));
    }
    
    public function fetchPayment() {
        return self::curl_get('https://api.tosspayments.com/v1/payments/'.$this->paymentKey,
        ['Authorization: '.$this->basicToken],
        null);
    }
    
    public function fetchOrder() {
        return self::curl_get('https://api.tosspayments.com/v1/payments/orders'.$this->orderId,
        ['Authorization: '.$this->basicToken],
        null);
    }
    
    public function billingAuth($cardNum, $cardExpY, $cardExpM, $cardPw, $custBirth, $custKey) {
        return self::curl_post('https://api.tosspayments.com/v1/billing/authorizations/card',
        ['Authorization: '.$this->basicToken,
        'Content-Type: application/json'],
        [
            'cardNumber' => $cardNum,
            'cardExpirationYear' => $cardExpY,
            'cardExpirationMonth' => $cardExpM,
            'cardPassword' => $cardPw,
            'customerBirthday' => $custBirth,
            'customerKey' => $custKey
        ]);
    }
    
    public function cardPromotions() {
        return self::curl_get('https://api.tosspayments.com/v1/promotions/card',
        ['Authorization: '.$this->basicToken],
        null);
    }
}
