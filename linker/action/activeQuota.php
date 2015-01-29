<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Active Volume</title>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Pasenger
 * Date: 2015/1/20
 * Time: 21:29
 */

$sigInterfaceName = "/activeQuota_signature/v1?";
$activeInterfaceName = "/activeQuota/v1?";

$serviceUrl = $_GET["serviceUrl"];
$activeCode = $_GET["activeCode"];
$uuid = $_GET["uuid"];
$appId = $_GET["appId"];
$promotionId = $_GET["promotionId"];
$activeVolume = $_GET["activeVolume"];
$callbackUrl = $_GET["callbackUrl"];
$redirectUrl = $_GET["redirectUrl"];
$signatureVersion = $_GET["signatureVersion"];

$expireTime = "";
if($serviceUrl == "http://182.92.236.103:8880"){
    $expireTime = date('Y-m-d H:i:s', strtotime("tomorrow"));
}else{
    $expireTime = date('YmdHis', strtotime("tomorrow"));
}


$timeStamp = date('YmdHis') . substr(microtime(), 2, 3);

$params = "activeCode=" . $activeCode .
    "&uuid=" . $uuid .
    "&appId=" . $appId .
    "&promotionId=" . $promotionId .
    "&callbackUrl=" . $callbackUrl .
    "&redirectUrl=" . $redirectUrl .
    "&activeVolume=" . $activeVolume .
    "&expireTime=" . $expireTime .
    "&timeStamp=" . $timeStamp .
    "&signatureVersion=" . $signatureVersion;

$signatureUrl = $serviceUrl . $sigInterfaceName . $params;

echo "签名链接：" . $signatureUrl . "<br><br>";

$sig = curl_init();
curl_setopt($sig, CURLOPT_URL, $signatureUrl);
curl_setopt($sig, CURLOPT_TIMEOUT, 3);
curl_setopt($sig, CURLOPT_HEADER, 1);
curl_setopt($sig, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($sig, CURLOPT_NOBODY, false);

$sigResponse = curl_exec($sig);

if (curl_getinfo($sig, CURLINFO_HTTP_CODE) == '200') {
    $headerSize = curl_getinfo($sig, CURLINFO_HEADER_SIZE);
    //$header = substr($response, $headerSize);
    $signuture = substr($sigResponse, $headerSize);

    curl_close($sig);

    $activeUrl = $serviceUrl . $activeInterfaceName . $params . "&signature=" . $signuture;

    echo "激活链接：" . $activeUrl . "<br><br>";

    $active = curl_init();
    curl_setopt($active, CURLOPT_URL, $activeUrl);
    curl_setopt($active, CURLOPT_TIMEOUT, 3);
    curl_setopt($active, CURLOPT_HEADER, 1);
    curl_setopt($active, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($active, CURLOPT_NOBODY, false);

    $activeResponse = curl_exec($active);

    echo "激活手机号：" . $uuid . "<br>激活APP： " . $appId . "<br>激活活动： " . $promotionId .
        "<br>激活流量：" . $activeVolume . "<br>激活结果：";

    if (curl_getinfo($active, CURLINFO_HTTP_CODE) == '200') {
        $headerSize = curl_getinfo($active, CURLINFO_HEADER_SIZE);
        //$header = substr($response, $headerSize);
        $result = substr($activeResponse, $headerSize);

        echo $result;
    } else {
        echo "active quota failed!<br>";
        echo $activeResponse;
    }

    curl_close($active);
} else {
    echo "calc signuture failed";
    echo "<br>";
    echo $sigResponse;
    return;
}
/*
http://localhost:8880/activeQuota/v1?
uuid=13693057265&
activeCode=1231101&
appId=360&
promotionId=100&
expireTime=2014-12-31%2018:00:00&
callbackUrl=&
redirectUrl=&
activeVolume=1048675&
timeStamp=201412301740123&
signatureVersion=v1&
signature=380ddb8f61745c0db3a7efce00c39f10c0421f9aae53b425d241c17ffa547d7c
*/
?>
</body>
</html>