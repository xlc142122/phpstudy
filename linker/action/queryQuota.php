<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>余量查询</title>
</head>
<body>
<?php

$sigInterfaceName = "/queryUserAppQuota_signature/v1?";
$activeInterfaceName = "/queryUserAppQuota/v1?";

$serviceUrl = $_GET["serviceUrl"];
$uuid = $_GET["uuid"];
$appId = $_GET["appId"];
$signatureVersion = $_GET["signatureVersion"];

$expireTime = date('YmdHis', strtotime("tomorrow"));
$timeStamp = date('YmdHis') . substr(microtime(), 2, 3);

$params = "&uuid=" . $uuid .
    "&appId=" . $appId .
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

    echo "手机号：" . $uuid . "<br>APPID： " . $appId .  "<br>查询结果：";

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
?>
</body>
</html>