<?php
/**
 * Created by sky.
 * User: sky
 * Date: 2018/1/19
 * Time: 11:15
 * @desc www.yyblogs.net
 */

/**
 * @desc 返回各第三方的信息
 * Class TypeInfo
 */
class TypeInfo
{
    /**
     * @desc 构造方法 实例ThirdOauth
     * TypeInfo constructor.
     * @param string $type
     * @param string $token
     * @throws Exception
     */
    public function __construct()
    {

    }

    /**
     * @desc 获取各平台的授权信息
     * @param string $type
     * @param string $token
     * @throws Exception
     */
    public function getThirdUserInfo($type = '', $token = '')
    {
        if (empty($type) || empty($token))
        {
            throw new \Exception('参数错误！');
        }
        switch ($type)
        {
            case 'FCLOGIN':
                return $this->faceBook($type, $token);
                break;
            case 'YTBLOGIN':
                return $this->youTuBe($type, $token);
                break;
            case 'TWLOGIN':
                return $this->twitter($type, $token);
                break;
            case 'INSLOGIN':
                return $this->instagram($type, $token);
                break;
            default:
                throw  new \Exception('参数不正确');
        }
    }

    /**
     * @desc  获取YouTube用户信息
     * @param $type
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public function youTuBe($type, $token)
    {
        $type_instance = ThirdOauth::getInstance($type, $token);

        $data = $type_instance->call('userinfo');

        if (is_array($data) && !empty($data))
        {
            $userInfo['type'] = 'GOOGLE';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['picture'];
            $userInfo['id']   = $data['id'];
            $userInfo['email']   = $data['email'];
            $userInfo['locale']  = $data['locale'];

            return $userInfo;
        }
        else
        {
            throw new \Exception("获取Google用户信息失败：{$data}");
        }
    }

    /**
     * @desc facebook信息获取
     */
    public function faceBook($type, $token)
    {
        echo 'facebook';
    }
    /**
     * @desc twitter信息获取
     */
    public function twitter($type, $token)
    {
        echo 'twitter';
    }
    /**
     * @desc instagram信息获取
     */
    public function instagram($type, $token)
    {
        echo 'instagram';
    }
}