<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 後花園 - 發送表單函式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\libraries;

class WebForm
{
    protected $method = 'GET';

    protected $url = '';

    protected $fields = array();

    public function method($method)
    {
        $this->method = $method;
    }
    public function action($url)
    {
        $this->url = $url;
    }

    public function addField($name, $value)
    {
        $this->fields[$name] = $value;
    }
    public function submit()
    {
        $ch = curl_init();

        $header[] = "Accept: application/json";
        $header[] = "Accept-Encoding: gzip";
        //添加HTTP header頭采用壓縮和GET方式請求
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        //curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
        if (strtoupper($this->method) == 'POST') {
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
            curl_setopt($ch, CURLOPT_POST, true);
            //echo "in\r\n";
        }
        
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}
