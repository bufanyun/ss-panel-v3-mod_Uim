<?php

namespace App\Utils;

/**
 * Class Common 公共函数
 * @package App\Utils
 */

class Common
{


    /**
     * @Notes:
     * post--curl
     * @Interface Post
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/22   23:55
     * @param array $curlPost
     * @param $url
     * @return bool|string
     */
    public static function Post($curlPost=[],$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }


    public static function is_url($url){
        $r = "/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is";
        if(preg_match($r,$url)){
            return true;
        }else{
            return false;
        }
    }


    public static function is_domain( $domain)
    {
        //所有域名后缀
        $arr = array(
            'com', 'cn', 'net', 'org', 'hk',
            'cc', 'info', 'biz', 'mobi', 'us',
            'me', 'co', 'jp', 'edu', 'tv',
            'la', 'top', 'vip', 'pw', 'best',
            'xyz', 'shop', 'ltd', 'club', 'wang',
            'online', 'love', 'art', 'site', 'xin',
            'store', 'tech', 'fun', 'cloud', 'website',
            'press', 'space', 'beer', 'luxe', 'video',
            'ren', 'group', 'fit', 'yoga', 'pro',
            'ink', 'info', 'design', 'link', 'work',
            'mobi', 'org', 'name', 'tv', 'co',
            'asia', 'show', 'city','tk',
        );

            $domain = explode('.',$domain);
            if($domain[1] == null){
                return false;
            }
            $end = end($domain);
            if(!in_array($end,$arr)){
                return false;
            }
            return true;
    }

}
