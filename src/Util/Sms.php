<?php

namespace App\Util;

use GuzzleHttp\Client;

class Sms
{
    private $baseUri = 'https://dysmsapi.aliyuncs.com';

    private $accessKeyId = 'LTAIndzq0QFjymw7';

    private $accessSecret = 'rvqLfg4pqIGYWJGTbxZZwPeogi7eKC&';

    public function send($number, $signName, $templateCode, $templateParam = array())
    {
        $now = new \DateTime();
        $now->setTimeZone(new \DateTimeZone("GMT"));
        $timestamp = $now->format('Y-m-d\TH:i:s\Z');

        $parameters = array();
        $parameters['AccessKeyId'] = $this->accessKeyId;
        $parameters['Timestamp'] = $timestamp; 
        $parameters['Format'] = 'JSON';
        $parameters['SignatureMethod'] = 'HMAC-SHA1';
        $parameters['SignatureVersion'] = '1.0';
        $parameters['SignatureNonce'] = $this->generateNonceStr();
        $parameters['Action'] = 'SendSms';
        $parameters['Version'] = '2017-05-25';
        $parameters['RegionId'] = 'cn-hangzhou';
        $parameters['PhoneNumbers'] = $number;
        $parameters['SignName'] = $signName;
        $parameters['TemplateCode'] = $templateCode;
        $parameters['OutId'] = "1234";

        if (count($templateParam) > 0) {
            $parameters['TemplateParam'] = json_encode($templateParam);
        }
        $parameters['Signature'] = $this->getSignCode($parameters);

        $client = new Client();
        try {
            $response = $client->request('POST', $this->baseUri, array(
                'form_params' => $parameters
            ));
            $result = $response->getBody()->getContents();
        } catch (\Exception $e) {
            $result = $e->getResponse()->getBody()->getContents();
        }
        $resultArr = json_decode($result, true);

        if ($resultArr['Code'] == "OK") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取参数签名字符串
     */
    private function getSignCode ($parameters)
    {
        ksort($parameters);
        $stringToSign = 'POST&'.$this->specialUrlEncode('/').'&';

        $tmp = "";
        foreach($parameters as $key=>$val){ 
            $tmp .= '&' . $this->specialUrlEncode($key) . '=' . $this->specialUrlEncode($val);
        } 
        $tmp = trim($tmp, '&');
        $stringToSign = $stringToSign . $this->specialUrlEncode($tmp);

        return base64_encode(hash_hmac('sha1', $stringToSign, $this->accessSecret, true));
    }

    /**
     * 阿里云POP协议Encode
     */
    private function specialUrlEncode($str) {
        $str = urlencode($str);
        $str = str_replace("+", "%20", $str);
        $str = str_replace("*", "%2A", $str);
        $str = str_replace("%7E", "~", $str);
        return $str; 
    }

    /**                        
     * 生成随机字符串
     */
    private function generateNonceStr() 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";

        $str ="";
        for ( $i = 0; $i < 32; $i++ )  {    
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }

        return $str;
    }
}
