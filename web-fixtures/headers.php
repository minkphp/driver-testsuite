<!DOCTYPE html>
<html>
<head>
    <title>Headers page</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
<pre>
    <?php
    require_once 'utils.php';
    echo html_escape_value(mink_dump($_SERVER));
    ?>
</pre>
</body>
</html>
