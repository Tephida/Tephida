<?php

declare(strict_types=1);

namespace Mozg\classes;

/**
 * Freekassa - Payment system
 */
class FKWallet
{
    private string $apiUrl = 'https://api.freekassa.ru/v1/';

    /**
     * @return array
     */
    public function config(): array
    {
        return require ENGINE_DIR . '/data/fwkassa.php';
    }

    /**
     * @throws \JsonException
     */
    public function getSignature(): array
    {
        $config_payment = $this->config();
        $api_key = $config_payment['key'];

        $wallet_data = [
            'shopId'=>$config_payment['merchant_id'],
            'nonce'=>time(),
        ];
        ksort($wallet_data);
        $sign = hash_hmac('sha256', implode('|', $wallet_data), $api_key);
        $wallet_data['signature'] = $sign;

        return $this->curlPost('balance', $wallet_data);
    }

    public function createOrder($wallet_data)
    {
        return $this->curlPost('orders/create', $wallet_data);
    }

    /**
     * @throws \JsonException
     */
    public function curlPost(string $url, array $wallet_data): array
    {
        ksort($wallet_data);
        $config_payment = $this->config();
        $api_key = $config_payment['key'];
        $signature = hash_hmac('sha256', implode('|', $wallet_data), $api_key);
        $wallet_data['signature'] = $signature;
//        var_dump($wallet_data);
        $request = json_encode($wallet_data, JSON_THROW_ON_ERROR);

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $this->apiUrl.$url);
        curl_setopt($curl_handle, CURLOPT_HEADER, 0);
        curl_setopt($curl_handle, CURLOPT_FAILONERROR, 0);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
        $result = trim(curl_exec($curl_handle));
        curl_close($curl_handle);

        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }
}