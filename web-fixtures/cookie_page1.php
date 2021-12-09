<?php
if (!isset($cookieAtRootPath)) {
    $cookieAtRootPath = true;
}

if (!isset($cookieValue)) {
    $cookieValue = 'srv_var_is_set';
}

setcookie('srvr_cookie', $cookieValue, 0, $cookieAtRootPath ? '/' : '');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Basic Form</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    Basic Page With Cookie Set from Server Side
</body>
</html>
