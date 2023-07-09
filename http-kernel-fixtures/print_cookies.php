<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Cookies page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
<pre>
    <?php
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $cookies = $request->cookies->all();
    unset($cookies['MOCKSESSID']);

    if (isset($cookies['srvr_cookie'])) {
        $srvrCookie = $cookies['srvr_cookie'];
        unset($cookies['srvr_cookie']);
        $cookies['_SESS'] = '';
        $cookies['srvr_cookie'] = $srvrCookie;
    }

    echo html_escape_value(mink_dump($cookies));
    ?>
</pre>
</body>
</html>
