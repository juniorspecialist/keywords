<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15.05.15
 * Time: 13:49
 */

namespace app\models;


use Yii;
use yii\base\Object;

//https://github.com/gonimar/yii2-robokassa

class Robokassa extends Object
{
    public $sMerchantLogin;
    public $sMerchantPass1;
    public $sMerchantPass2;
    public $testMode = false;
    public $baseUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    public function payment($nOutSum, $nInvId, $sInvDesc = null, $sIncCurrLabel=null, $sEmail = null, $sCulture = null, $shp = [])
    {
        $url = $this->baseUrl;
        $signature = "{$this->sMerchantLogin}:{$nOutSum}:{$nInvId}:{$this->sMerchantPass1}";
        if (!empty($shp)) {
            $signature .= ':' . $this->implodeShp($shp);
        }
        $sSignatureValue = md5($signature);
        $url .= '?' . http_build_query([
                'MrchLogin' => $this->sMerchantLogin,
                'OutSum' => $nOutSum,
                'InvId' => $nInvId,
                'Desc' => $sInvDesc,
                'SignatureValue' => $sSignatureValue,
                'IncCurrLabel' => $sIncCurrLabel,
                'Email' => $sEmail,
                'Culture' => $sCulture,
            ]);
        if (!empty($shp) && ($query = http_build_query($shp)) !== '') {
            $url .= '&' . $query;
        }
        Yii::$app->user->setReturnUrl(Yii::$app->request->getUrl());
        return Yii::$app->response->redirect($url);
    }

    public function init(){

        parent::init();

        //if set testMode we used special URL for send params
        if($this->testMode){
            $this->baseUrl = 'http://test.robokassa.ru/Index.aspx';
        }
    }

    private function implodeShp($shp)
    {
        ksort($shp);
        foreach($shp as $key => $value) {
            $shp[$key] = $key . '=' . $value;
        }
        return implode(':', $shp);
    }
    public  function checkSignature($sSignatureValue, $nOutSum, $nInvId, $sMerchantPass, $shp)
    {
        $signature = "{$nOutSum}:{$nInvId}:{$sMerchantPass}";
        if (!empty($shp)) {
            $signature .= ':' . $this->implodeShp($shp);
        }
        return strtolower(md5($signature)) === strtolower($sSignatureValue);
    }
}