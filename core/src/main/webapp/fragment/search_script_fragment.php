<!--
<script src="http://code.jquery.com/jquery-1.8.2.js">
</script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js">
</script>
-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<!-- latest jqueryui is 1.9, but it is not hosted yet by Google -->
<script src="script/js/search.js"></script>
<script>
<?php
//Convert stations from ISO-8859-2 to UTF-8
$url = "http://elvira.mav-start.hu/elvira.js";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, '3');
$response = trim(curl_exec($ch));
curl_close($ch);

$utf8_result = iconv("ISO-8859-2", "UTF-8", $response);
echo $utf8_result;
?>
</script>