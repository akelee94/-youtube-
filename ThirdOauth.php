<?php
/**
 * Created by sky.
 * User: sky
 * Date: 2018/1/17
 * Time: 17:39
 * @desc www.yyblogs.net
 */

abstract class ThirdOauth
{
    /**
     * @desc 申请账号时分配的appkey
     * @var string
     */
    protected $AppKey = '';

    /**
     * @desc 申请账号时返回的appsecret
     * @var string
     */
    protected $AppSecret = '';

    /**
     * @desc 版本账号 version
     * @var string
     */
    protected $AppVersion = '';

    /**
     * @desc 回调Url
     *
     * @var string
     */
    protected $CallBack = '';

    /**
     * @desc   获取request_code请求的url
     * @var string
     */
    protected $GetRequestCodeUrl = '';

    /**
     * @desc 获取access_token请求的url
     *
     * @var string
     */
    protected $GetAccessTokenUrl = '';

    /**
     * @desc 获取request_code的额外参数，url查询字符串格式
     *
     * @var string
     */
    protected $Authorize = '';

    /**
     * 授权类型 response_type 目前只能为code
     * @var string
     */
    protected $ResponseType = 'code';

    /**
     * grant_type 目前只能为 authorization_code
     * @var string
     */
    protected $GrantType = 'authorization_code';

    /**
     * @desc Api的根路径
     *
     * @var string
     */
    protected $ApiBase = '';

    /**
     * @desc 授权后获取到的TOKEN信息
     *
     * @var array
     */
    protected $Token = null;

    /**
     * 调用接口类型
     * @var string
     */
    protected $ThirdLoginType = '';

    /**
     * @desc 构造方法  获取配置信息
     * ThirdOauth constructor.
     * @throws Exception
     */
    public function __construct($token = null)
    {
        if ($this->ThirdLoginType == 'FCLOGIN')
        {
            $config = C('third_config.facebook');  //以下C方法都是自己框架封装调用 配置文件的配置信息
        }
        elseif ($this->ThirdLoginType == 'YTBLOGIN')
        {
            $config = C('third_config.youtube');
        }
        elseif ($this->ThirdLoginType == 'TWLOGIN')
        {
            $config = C('third_config.twitter');
        }
        elseif ($this->ThirdLoginType == 'INSLOGIN')
        {
            $config = C('third_config.instagram');
        }

        if (empty($config['app_key']) || empty($config['app_secret']) || empty($config['app_callback']))
        {
            throw new \Exception('请配您申请的APP_KEY和APP_SECRET和APP_CALLBACK');
        }
        else
        {
            $this->AppKey = $config['app_key'];

            $this->AppSecret = $config['app_secret'];

            $this->AppVersion = $config['app_version'];

            $this->CallBack  = $config['app_callback'];

            $this->Authorize = $config['app_authorize'];

            $this->Token = $token;
        }
    }

    /**
     * @desc 获取实例
     *
     * @param $type
     */
    public static function getInstance($type, $token = null)
    {
        if ($type == 'FCLOGIN')
        {
            //调用facebook类

            echo  '我是facebook';
        }

        if ($type == 'YTBLOGIN')
        {
            //挑用YouTube类
            require_once '';//sdk中的对应实例化的类

            return new GoogleSDK($token);
        }

        if ($type == 'TWLOGIN')
        {
            //调用twitter类
            echo  '我是twitter';
        }

        if ($type == 'INSLOGIN')
        {
            //调用Instagram类
            echo  '我是Instagram';
        }
    }

    /**
     * @desc 请求url获取code码
     *
     * @return string
     * @throws Exception
     */
    public function getRequestCode()
    {
        //第三发对应的参数规则
        if ($this->ThirdLoginType == 'FCLOGIN')
        {
            $params = [
                'appID'         => $this->AppKey,
                'redirect_uri'  => $this->Callback,
                'response_type' => $this->ResponseType
            ];
        }
        elseif ($this->ThirdLoginType == 'YTBLOGIN')
        {
            $params = [
                'client_id'     => $this->AppKey,
                'redirect_uri'  => $this->CallBack,
                'response_type' => $this->ResponseType
            ];
        }
        elseif ($this->ThirdLoginType == 'TWLOGIN')
        {

        }
        elseif ($this->ThirdLoginType == 'INSLOGIN')
        {

        }
        //获取额外参数  组装数据
        if (!empty($this->Authorize))
        {
            parse_str($this->Authorize, $_params);

            if (is_array($_params))
            {
                $params = array_merge($params, $_params);
            }
            else
            {
                throw new \Exception('额外参数配置不正确');
            }
        }
        return $this->GetRequestCodeUrl . '?' .http_build_query($params);
    }

    /**
     * @desc 获取access_token
     * @param string $code 上一步请求到的code
     * @return array|mixed
     * @throws Exception
     */
    public function getAccessToken($code, $extend = null)
    {
        if ($this->ThirdLoginType == 'FCLOGIN')
        {
            $params = [
                'appID'  => $this->AppKey,
                'secret' => $this->AppSecret,
                'redirect_uri'  => $this->CallBack,
                'code'          => $code,
                'grant_type' => $this->GrantType
            ];
        }
        elseif ($this->ThirdLoginType == 'YTBLOGIN')
        {
            $params = [
                'client_id'     => $this->AppKey,
                'client_secret' => $this->AppSecret,
                'grant_type'    => $this->GrantType,
                'code'          => $code,
                'redirect_uri'  => $this->CallBack,
            ];
        }
        elseif ($this->ThirdLoginType == 'TWLOGIN')
        {

        }
        elseif ($this->ThirdLoginType == 'INSLOGIN')
        {

        }
    
        //此步骤可以保存session，不然授权之后刷新界面会报错
        $data = $this->curlHttp($this->GetAccessTokenUrl, $params, 'POST');

        $this->Token = $this->parseToken($data, $extend);

        return $this->Token;
    }

    /**
     * 合并默认参数和额外参数
     * @param array $params  默认参数
     * @param array/string $param 额外参数
     * @return array:
     */
    protected function param($params, $param){
        if(is_string($param))
            parse_str($param, $param);
        return array_merge($params, $param);
    }

    /**
     * 获取指定API请求的URL
     * @param  string $api API名称
     * @param  string $fix api后缀
     * @return string      请求的完整URL
     */
    protected function url($api, $fix = ''){
        return $this->ApiBase . $api . $fix;
    }

    /**
     * @desc 发送http请求 ，只支持curl请求
     *
     * @param $url
     * @param $params
     * @param string $method
     * @param array $header
     * @param bool $multi
     * @return mixed
     * @throws Exception
     */
    protected function curlHttp($url, $params, $method = 'GET', $header = [], $multi =false)
    {
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new \Exception('请求发生错误：' . $error);
        return  $data;
    }

    /**
     * 抽象方法，在SNSSDK中实现
     * 组装接口调用参数 并调用接口
     */
    abstract protected function call($api, $param = '', $method = 'GET', $multi = false);

    /**
     * 抽象方法，在SNSSDK中实现
     * 解析access_token方法请求后的返回值
     */
    abstract protected function parseToken($result, $extend);

    /**
     * 抽象方法，在SNSSDK中实现
     * 获取当前授权用户的SNS标识
     */
    abstract public function openid();
}