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

$sigInterfaceName = "/activeARQuota_signature/v1?";
$activeInterfaceName = "/activeARQuota/v1?";

$serviceUrl = $_GET["serviceUrl"];
$activeCode = $_GET["activeCode"];
$uuid = $_GET["uuid"];
$appId = $_GET["appId"];
$promotionId = $_GET["promotionId"];
$trafficPackage = $_GET["trafficPackage"];
$callbackUrl = $_GET["callbackUrl"];
$redirectUrl = $_GET["redirectUrl"];
$signatureVersion = $_GET["signatureVersion"];

$expireTime = date('YmdHis', strtotime("tomorrow"));
$timeStamp = date('YmdHis') . substr(microtime(), 2, 3);

$params = "activeCode=" . $activeCode .
    "&uuid=" . $uuid .
    "&appId=" . $appId .
    "&promotionId=" . $promotionId .
    "&callbackUrl=" . $callbackUrl .
    "&redirectUrl=" . $redirectUrl .
    "&trafficPackage=" . $trafficPackage .
    "&expireTime=" . $expireTime .
    "&timeStamp=" . $timeStamp .
    "&signatureVersion=" . $signatureVersion;

$signatureUrl = $serviceUrl . $sigInterfaceName . $params;

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

    $active = curl_init();
    curl_setopt($active, CURLOPT_URL, $activeUrl);
    curl_setopt($active, CURLOPT_TIMEOUT, 3);
    curl_setopt($active, CURLOPT_HEADER, 1);
    curl_setopt($active, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($active, CURLOPT_NOBODY, false);

    $activeResponse = curl_exec($active);

    echo "<br><br>签名链接：<br>";
    echo $signatureUrl;
    echo "<br><br>激活链接：<br>";
    echo $activeUrl;
    echo "<br><br>";
    echo "激活手机号：" . $uuid . "<br>激活APP： " . $appId . "<br>激活活动： " . $promotionId .
        "<br>签名：" . $signuture .
        "<br>激活流量包：" . $trafficPackage . "<br>激活结果：";

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
    echo $signatureUrl;
    echo "<br>";
    echo $sigResponse;
    return;
}

?>
</body>
</html>