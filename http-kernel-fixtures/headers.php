<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Headers page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
<pre>
    <?php
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    echo html_escape_value(mink_dump($request->server->all()));
    ?>
</pre>
</body>
</html>
