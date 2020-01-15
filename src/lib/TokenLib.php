<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 14:39
 */

namespace jd\biz\lib;


class TokenLib
{

    public function getAccessToken()
    {
        /**
         * 判断是否存在缓存accessToken,存在并有效就返回
         * 无效时,判断是否有refreshToken
         * 有,根据refreshToken去重新获取token
         * 无,第一次请求accessToken
         * 获取到accessToken和refreshToken时,都需要重新保存更新缓存
         */
        try{
            $accessTokenFile = $this->getAccessTokenFile();
            if(is_file($accessTokenFile)){
                $accessTokenData = file_get_contents($accessTokenFile);
                if(!empty($accessTokenData)){
                    $data = json_decode(base64_decode($accessTokenData), true);
                    //缓存24小时有效,留5分钟空档
                    if((time() - $data['expires_in']) <= 86100){
                        return HelperLib::returnSuc([
                            'accessToken' => $data['access_token']
                        ]);
                    }
                    $tokenData = $this->getRefreshToken($data['refresh_token']);
                }
            }
            //调用获取accessToken接口
            if(!isset($tokenData)){
                $tokenData = $this->getTokenFromJd();
            }
            if ($tokenData['code'] === 0){
                return HelperLib::returnSuc(['accessToken' => $tokenData['data']['access_token']]);
            }else{
                return $tokenData;
            }

        }catch (\Exception $exception){
            return HelperLib::returnMsg(4010, $exception->getMessage());
        }
    }

    /**
     * 获取access_token信息
     * @return array
     */
    protected function getTokenFromJd()
    {
        $url = ConfigLib::get('jdBaseUrl').'oauth2/accessToken';
        $postData = [
            'grant_type' => 'access_token',
            'client_id' => ConfigLib::get('client_id'),
            'timestamp' => $this->getTimestamp(),
            'username' => ConfigLib::get('username'),
            'password' => $this->getPassword(),
            'sign' => $this->getSign()
        ];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $response = QueryTools::query($url, $postData, $headers);
        if($response['code'] === 0){
            $accessTokenData = $this->saveAccessToken($response['data']);
            return HelperLib::returnSuc($accessTokenData);
        }
        return $response;

    }

    /**
     * 根据refreshToken刷新accessToken
     * @param $refreshToken
     * @return array
     */
    protected function getRefreshToken($refreshToken)
    {
        $url = ConfigLib::get('jdBaseUrl').'oauth2/refreshToken';
        $postData = [
            'refresh_token' => $refreshToken,
            'client_id' => ConfigLib::get('client_id'),
            'client_secret' => ConfigLib::get('client_secret')
        ];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $response = QueryTools::query($url, $postData, $headers);
        if($response['code'] === 0){
            $accessTokenData = $this->saveAccessToken($response['data']);
            return HelperLib::returnSuc($accessTokenData);
        }
        return $response;
    }


    /**
     * 缓存accessToken
     * @param $accessTokenData
     * @return mixed
     */
    protected function saveAccessToken($accessTokenData)
    {
        $accessTokenData['expires_in'] = time()+$accessTokenData['expires_in'];
        $accessTokenData['refresh_token_expires'] = intval($accessTokenData['refresh_token_expires']/1000);
        $accessTokenData['time'] = intval($accessTokenData['time']/1000);

        $accessTokenFile = $this->getAccessTokenFile();
        $data = base64_encode(json_encode($accessTokenData, JSON_UNESCAPED_UNICODE));
        file_put_contents($accessTokenFile, $data);

        return $accessTokenData;
    }

    protected function getSign()
    {
        $grantType = 'access_token';
        $timestamp = $this->getTimestamp();
        $clientSecret = ConfigLib::get('client_secret');
        $clientId = ConfigLib::get('client_id');
        $username = ConfigLib::get('username');
        $password = $this->getPassword();

        $signString = $clientSecret.$timestamp.$clientId.$username.$password.$grantType.$clientSecret;

        return strtoupper(md5($signString));
    }

    protected function getTimestamp()
    {
        return date('Y-m-d H:i:s');
    }

    protected function getPassword()
    {
        return md5(ConfigLib::get('password'));
    }

    protected function getAccessTokenFile()
    {
        return __DIR__.'/../storage/'.base64_encode(ConfigLib::get('client_id')).'_access_token.log';
    }
}