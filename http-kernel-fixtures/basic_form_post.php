<?php /** @var \Symfony\Component\HttpFoundation\Request $request */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Basic Form Saving</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <h1>Anket for <?php echo html_escape_value((string) $request->request->get('first_name')) ?></h1>

    <span id="first">Firstname: <?php echo html_escape_value((string) $request->request->get('first_name')) ?></span>
    <span id="last">Lastname: <?php echo html_escape_value((string) $request->request->get('last_name')) ?></span>
</body>
</html>
