<?
require 'CloudFront.php';

// your AWS/CloudFront keys go here
$keyId = "";
$secretKey = "";
$distributionId = "";

$key = ""; // String representing the existing CloudFront object to invalidate

$cf = new CloudFront($keyId, $secretKey, $distributionId);
$cf->invalidate($key);

?>
<html>
<head>
    <style> textarea {
        width: 100%;
        height: 600px;
        font: 12px/16px consolas;
    } </style>
</head>
<body>
Key: <?=$key?><br/>
<hr/>
CF call:<br/>
<!--Passing "true" to enable debugging for the purpose of this example.
This will render the XML response.-->

<textarea><?=$cf->getResponseMessage()?></textarea>
</body>
</html>