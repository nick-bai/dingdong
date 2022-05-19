<?php
// 应用公共文件
/**
 * 根据ip定位
 * @param $ip
 * @param $type
 * @return string | array
 * @throws Exception
 */
function getLocationByIp($ip, $type = 1)
{
    $ip2region = new \Ip2Region();
    $info = $ip2region->btreeSearch($ip);

    $info = explode('|', $info['region']);

    $address = '';
    foreach($info as $vo) {
        if('0' !== $vo) {
            $address .= $vo . '-';
        }
    }

    if (2 == $type) {
        return ['province' => $info['2'], 'city' => $info['3']];
    }

    return rtrim($address, '-');
}

/**
 * 设置jwt
 * @param $data
 * @return string
 */
function setJWT($data) {
    $jwt   = new Firebase\JWT\JWT();
    $token = [
        // "iss"  => "http://example.org", // 签发者
        // "aud"  => "http://example.com", // 认证者
        'iat'  => time(), // 签发时间
        'nbf'  => time(), // 生效时间
        'exp'  => (time() + 60 * 60 * 24 * 7), // 过期时间  7天后的时间戳
        'data' => $data
    ];
    $jwt = $jwt::encode($token, \config('dingdong.jwt_key'), 'HS256');
    return $jwt;
}

/**
 * 获取token中的信息
 * @param $token
 * @return array|null
 */
function getJWT($token) {
    $jwt  = new Firebase\JWT\JWT();
    $data = [];
    try {
        $jwt_data = $jwt::decode($token, new Firebase\JWT\Key(\config('dingdong.jwt_key'), 'HS256'));
        $data     = (array) ($jwt_data->data);
    } catch (\Exception $e) {
        \think\facade\Log::write($e->getMessage(), 'error');
        return null;
    }
    return $data;
}

/**
 * 从头部获取token
 * @return bool|string
 */
function getHeaderToken() {
    $header = request()->header();
    if (isset($header['authorization'])) {
        return substr($header['authorization'], 7);
    }

    return '';
}

function jsonReturn($code, $msg = 'success', $data = []) {

    return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
}

function crossDomain() {

    header("access-control-allow-headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With");
    header("access-control-allow-methods: OPTIONS,GET, POST, PATCH, PUT, DELETE");
    header("access-control-allow-origin: *");
}

/**
 * 计算时长
 * @param $seconds
 * @return string
 */
function changeTimeType($seconds)
{
    if ($seconds > 3600) {
        $hours = intval($seconds / 3600);
        $minutes = $seconds % 3600;
        $time = $hours . ":" . gmstrftime('%M:%S', $minutes);
    } else {
        $time = gmstrftime('%H:%M:%S', $seconds);
    }

    return $time;
}