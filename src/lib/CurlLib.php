<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 14:41
 */

namespace jd\biz\lib;


class CurlLib
{
    /**
     * opt参数
     * @var array
     */
    protected $optData = [];

    /**
     * http code
     * @var
     */
    protected $httpCode;

    /**
     * 默认超时时间
     * @var int
     */
    protected $timeout = 45;

    /**
     * curl header头
     * @var array
     */
    protected $header = [];

    /**
     * 设置opt参数
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOpt($key, $value)
    {
        $this->optData[$key] = $value;
        return $this;
    }

    /**
     * 获取opt参数
     * @param $key
     * @return bool|mixed
     */
    public function getOpt($key)
    {
        if(isset($this->optData[$key])){
            return $this->optData[$key];
        }
        return false;
    }

    /**
     * 设置多个header头
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key=>$value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * 设置header头
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->header[trim($key)] = trim($value);
        return $this;
    }

    /**
     * 设置https请求
     * @return $this
     */
    public function setHttps()
    {
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        return $this;
    }

    /**
     * 设置代理
     * @param $proxyAddress
     * @return $this
     */
    public function setProxy($proxyAddress)
    {
        $this->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        $this->setOpt(CURLOPT_PROXY, $proxyAddress);
        return $this;
    }

    /**
     * 获取http code
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }


    /**
     * 设置超时时间
     * @param int $timeout
     * @return $this
     */
    public function setTimeOut($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * get请求
     * @param $url
     * @return bool|string
     */
    public function get($url)
    {
        return $this->curlData($url);
    }

    /**
     * post请求
     * @param $url
     * @param string $postData
     * @return bool|string
     */
    public function post($url, $postData = '')
    {
        $this->setOpt(CURLOPT_POST, 1);
        $this->setOpt(CURLOPT_POSTFIELDS, $postData);

        return $this->curlData($url);
    }

    /**
     * 公共curl
     * @param $url
     * @return bool|string
     */
    protected function curlData($url)
    {
        $ch = curl_init();
        if (false == $ch) return false;

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => false, //true  false
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_RETURNTRANSFER => 1,
        );
        //增加设置opt
        if(!empty($this->optData)){
            foreach ($this->optData as $key => $value) {
                $options[$key] = $value;
            }
        }
        //设置header
        if (!empty($this->header)) {
            $header = [];
            foreach ($this->header as $key=>$value) {
                $header[] = $key.": ".$value;
            }
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $content;
    }

}