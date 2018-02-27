整合的YouTube信息以及视频列表信息和Facebook，Twitter的等等，第三方授权登陆接口封装

用于Youtube Data API v3（包含Auth）授权获取方法

## 配置注意事项
在项目中直接 git clone
```php
git clone     https://github.com/orchid-lyy/-youtube-.git
```

不需要配置环境（需要去YouTube官方申请key，并且在Google上开通YouTube Data API v3）权限。

你可以在实例化对象的时候传入key：

```php
$youtube = new Youtube('your api key);
```
或者也可以在类文件中youtube.php配置

```php
protected $key = null;//可设置null为你的key
```

## 大致使用方法


实例化类：
```php
$youtube = new Youtube('key');
```
```php
//通过授权获取该用户的channel 列表（频道列表必须授权，直接获取现在好像不支持了）

$youtube->getAllChannlIdByMine($access_token, $part = ['snippet', 'contentDetails', 'statistics']);


//根据YouTube用户的用户名获取channel列表（可尝试使用用户名获取）

$youtube->getChannelByUsername($username, $optionalParams = false, $part = ['snippet', 'contentDetails', 'statistics']);

//根据channelId 获取播放列表 

$youtube->getPlaylistsByChannelId($channelId, $part = ['snippet', 'contentDetails'], $optionalParams = []);

//通过授权access_token拿到 all playlists

$youtube->getAllPlaylistsByMine($access_token, $part = ['snippet', 'contentDetails']);

//通过播放列表ID 获取该下面项目集合

$youtube->getPlaylistItemsByPlaylistId($playlistId, $maxResults = 50, $pageToken = '', $part = ['snippet', 'contentDetails']);

//通过videoId 获取video详情

$youtube->getVideoInfoByVideoId($videoId, $part = ['snippet', 'contentDetails', 'statistics']);

具体的可以参考类中的方法！！！

```

后面还会接着维护，facebook，twitter等等，包括授权登录，会补上来的！！！

