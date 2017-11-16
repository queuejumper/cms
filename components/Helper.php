<?php
namespace app\components;

use Yii;
use \Datetime;
class Helper
{
        const   CIPHER = "aes-256-cbc",
                SSL_KEY = 'tHIs_iS_OuR_$ECrEt_KEY';
    
    public static function encrypt($data)
    {
        $result = false;   
        $ivlen = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $result = openssl_encrypt($data, self::CIPHER, self::SSL_KEY, $options=0, $iv);
        $result = strtr(base64_encode($result.'::'.$iv), '+/=', '._-');
        return $result;
        
    }

    public static function decrypt($data)
    {
        $result = false;
        list($decryptedData, $iv) = explode('::', base64_decode(strtr($data, '._-', '+/=')),2);
        $result = openssl_decrypt($decryptedData, self::CIPHER, self::SSL_KEY, $options=0, $iv);
        return $result;
    }


    public static function readJsonFile($path)
    {
        $file = file_get_contents(Yii::getAlias('@webroot').'/'.$path);
        return json_decode($file,true);
    }

    public static function formatNumber($number)
    {
        switch (true) {

            case $number >= 1000000000 :
                $number = $number / 1000000000;
                $result = round($number,1)."B";
                break;

            case $number >= 1000000:
                $number = $number / 1000000;
                $result = round($number,1)."M";
                break;       

            case $number >= 1000 :
                $number = $number / 1000;
                $result = round($number,1)."K";
                break;   

            default:
                $result = $number;
                break;
        }
        return $result;
    }


    public static function dateFrom($_date)
    {
        $now = new DateTime("now");
        $date = new DateTime($_date);
        $diff = $now->diff($date);

        $year = $diff->y;
        $month = $diff->m;
        $day = $diff->d;
        $hour = $diff->h;
        $minute = $diff->i;

        switch (true) {
            case $year > 0:
                if($year == 1){
                    $result = $year." year ago";
                }else {
                     $result = $year." years ago";
                }
                break;

            case $month > 0:
                if($month == 1){
                    $result = $month." month ago";
                }else {
                     $result = $month." months ago";
                }
                break;

            case $day > 6:
                $_week = $day % 7;
                $week = $day / 7;
                if($_week == 0){
                    if($week == 1){
                        $result = "1 week ago";
                    }else{
                        $result = $week." weeks ago";
                    }
                }else{
                    $week = round($week);
                    $result = "about ".$week." weeks ago";
                }
                break;  

            case $day < 6 && $day > 0:
                if($day == 1){
                    $result = $day." day ago";
                }else {
                     $result = $day." days ago";
                }
                break;  

            case $hour > 0:
                if($hour == 1){
                    $result = $hour." hour ago";
                }else {
                     $result = $hour." hours ago";
                }
                break;  

            case $minute > 0:
                if($minute == 1){
                    $result = $minute." minute ago";
                }else {
                     $result = $minute." minutes ago";
                }
                break;  

            default:
               $result = 'just now';
                break;
        }
        return $result;
    }




}