<?php
/**
 * Created by sky.
 * User: sky
 * Date: 2018/1/15
 * Time: 22:18
 * @desc www.yyblogs.net
 */

class Youtube
{
    /**
     * @desc youtube API key(秘钥)
     * @var null
     */
    protected $key = null;

    /**
     * @desc 分页信息
     * @var array
     */
    protected $page_info = [];

    protected $youtube_apis = [
        'channels.list'  => 'https://www.googleapis.com/youtube/v3/channels',  //获取channel列表
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists', //获取播放列表
        'videos.list'    => 'https://www.googleapis.com/youtube/v3/videos',     //获取视频列表
        'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',  //获取播放列表项目集合
        'categories.list'    => 'https://www.googleapis.com/youtube/v3/videoCategories',    //获取分类
        'search.list'    => 'https://www.googleapis.com/youtube/v3/search', // 搜索
        'activities'     => 'https://www.googleapis.com/youtube/v3/activities',//活动
        'subscriptions'  => 'https://www.googleapis.com/youtube/v3/subscriptions',//订阅
    ];

    /**
     * Youtube constructor.
     * @desc  $youtube = new Youtube('your api key);
     * @param $key  谷歌API key
     * @throws Exception
     */
    public function __construct($key)
    {
        if (!empty($key) && is_string($key))
        {
            $this->key = $key;
        }else{
            throw new \Exception('Google api key is null or error!');
        }
    }

    /**
     * @desc 通过授权获取该用户的channel 列表
     * @param $access_token  授权token
     * @param array $part    参数
     * @param bool $optionalParams  额外参数
     * @return mixed
     * @throws Exception        异常抛出
     */
    public function getAllChannlIdByMine($access_token, $part = ['snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('channels.list');

        $params = [
            'part' => implode(',', $part),
            'mine' => true,
            'access_token' => $access_token
        ];

        $result = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($result);
    }

    /**
     * @desc 根据YouTube用户的用户名获取channel列表
     * @param $username
     * @param bool $optionalParams
     * @param array $part
     * @return mixed
     * @throws Exception
     */
    public function getChannelByUsername($username, $optionalParams = false, $part = ['snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('channels.list');

        $params = [
            'forUsername' => $username,
            'part'        => implode(',', $part),
        ];

        if (!empty($optionalParams))
        {
            $params = array_merge($params, $optionalParams);
        }

        $result = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($result);
    }

    /**
     * @desc 通过channelh获取
     * @param $id
     * @param bool $optionalParams
     * @param array $part
     * @return mixed
     * @throws Exception
     */
    public function getChannelById($id, $optionalParams = false, $part = ['snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('channels.list');

        $params = [
            'id' => is_array($id) ? implode(',', $id) : $id,
            'part' => implode(', ', $part)
        ];
        if ($optionalParams)
        {
            $params = array_merge($params, $optionalParams);
        }

        $data = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($data);
    }

    /**
     * @desc 根据channelId 获取播放列表
     * @param $channelId
     * @param array $part
     * @param array $optionalParams
     * @return mixed
     * @throws Exception
     */
    public function getPlaylistsByChannelId($channelId, $part = ['snippet', 'contentDetails'], $optionalParams = [])
    {
        $API_URL = $this->getApi('playlists.list');

        $params = [
            'channelId' => $channelId,
            'part' => implode(', ', $part)
        ];

        if ($optionalParams) {

            $params = array_merge($params, $optionalParams);
        }

        $data = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($data);
    }

    /**
     * @desc 通过授权token拿到 all playlists
     * @param $access_token
     * @param array $part
     * @return mixed
     * @throws Exception
     */
    public function getAllPlaylistsByMine($access_token, $part = ['snippet', 'contentDetails'])
    {

        $API_URL = $this->getApi('playlists.list');

        $params = [
            'part' => implode(',', $part),
            'mine' => true,
            'access_token' => $access_token
        ];

        $result = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($result);
    }

    /**
     * @desc 通过播放列表ID 获取该下面项目集合
     * @param $playlistId
     * @param string $pageToken
     * @param int $maxResults
     * @param array $part
     * @return array
     * @throws Exception
     */
    public function getPlaylistItemsByPlaylistId($playlistId, $maxResults = 50, $pageToken = '', $part = ['snippet', 'contentDetails'])
    {
        $API_URL = $this->getApi('playlistItems.list');
        $params = [
            'playlistId' => $playlistId,
            'part' => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        $params['pageToken'] = $pageToken;

        $data = $this->curlHttp($API_URL, $params);

        $result = ['results' => $this->decodeJson($data)];

        return $result;
    }

    /**
     * @desc 通过videoId 获取video详情
     * @param $videoId
     * @param array $part
     * @return StdClass
     * @throws Exception
     */
    public function getVideoInfoByVideoId($videoId, $part = ['snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('videos.list');

        $params = [
            'id' => is_array($videoId) ? implode(',', $videoId) : $videoId,
            'part' => implode(', ', $part),
        ];

        $data = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($data);
    }

    /**
     * @desc 通过地区code 获取流行 在一个国家获取流行的视频
     * @param $regionCode
     * @param int $maxResults
     * @param array $part
     * @return mixed
     * @throws Exception
     */
    public function getPopularVideosByReginCode($regionCode, $maxResults = 25, $part = ['snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('videos.list');
        $params = [
            'regionCode' => $regionCode,
            'chart' => 'mostPopular',
            'part' => implode(', ', $part),
            'maxResults' => $maxResults,
        ];

        $data = $this->curlHttp($API_URL, $params);

        return $this->decodeJson($data);
    }



    /**
     * @desc 获取接口API
     * @param $name
     * @return mixed
     */
    public function getApi($name)
    {
        return $this->youtube_apis[$name];
    }
    /**
     * @desc 处理接口返回的数据结果
     * @param $resultData
     * @throws Exception
     */
    protected function decodeJson(&$resultData)
    {
        $result = json_decode($resultData, true);

        //判断对象是否设置
        if (isset($result->error))
        {
            $message = 'Error' . $result->error->code .''. $result->error->message;

            if (isset($result->error->errors[0]))
            {
                $message .= ':' . $result->error->errors[0]->reason;

                $message .= ':' . $result->error->errors[0]->message;
            }

            throw new \Exception($message);
        }

        return $result;
    }

    /**
     * @desc get 请求url数据
     * @param $url
     * @param $params
     * @return mixed
     * @throws Exception
     */
    protected function curlHttp($url, $params)
    {
        //拿到API key 组装数据
        $params['key'] = $this->key;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . $this->new_http_build_query($params));

        //判断请求端口 从而判断是否是http还是https请求
        if (strpos($url, 'https') === false) {

            curl_setopt($curl, CURLOPT_PORT, 80);
        } else {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);// https请求不验证证书和hosts

//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($curl);

        if (curl_errno($curl)) {

            throw new \Exception('Curl Error : ' . curl_error($curl));
        }

        return $result;
    }

    /**
     * @desc http_build_query 组装数据 修改true默认等于1的问题
     * @param $queryData   数据
     * @param string $numericPrefix 数字索引时附加的key的前缀
     * @param string $argSeparator  参数分隔符 默认是&
     * @param string $keyPrefix  key的前缀（共内部递归时使用）
     * @return string
     */
    protected function new_http_build_query($queryData, $numericPrefix = '', $argSeparator = '&', $keyPrefix = '') {
        $array = array();
        foreach ($queryData as $key => $val) {
            if ($val === NULL) {
                continue;
            }
            if (!is_array($val) && !is_object($val)) {
                if (is_bool($val)) {
                    $val = $val ? 'true' : 'false';
                }
                if ($keyPrefix === '') {
                    if (is_int($key)) {
                        $array[] = $numericPrefix . urlencode($key) . '=' . urlencode($val);
                    } else {
                        $array[] = urlencode($key) . '=' . urlencode($val);
                    }
                } else {
                    $array[] = urlencode($keyPrefix . '[' . $key . ']') . '=' . urlencode($val);
                }
            } else {
                if ($keyPrefix === '') {
                    $newKeyPrefix = $key;
                } else {
                    $newKeyPrefix = $keyPrefix . '[' . $key . ']';
                }
                $array[] = call_user_func_array(__FUNCTION__, array($val, $numericPrefix, $argSeparator, $newKeyPrefix));//调用回调函数
            }
        }
        return implode($argSeparator, $array);
    }
}