<?php
if (!isset($cookieValue)) {
    $cookieValue = 'srv_var_is_set';
}

$cookiePath = dirname($_SERVER['REQUEST_URI']) . '/';
setcookie('srvr_cookie', $cookieValue, 0, $cookiePath);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Basic page with cookie set in sub-directory from server side</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    Basic page with cookie set in sub-directory from server side
</body>
</html>
