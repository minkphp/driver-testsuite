<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>Advanced form save</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<pre>
<?php
error_reporting(0);

$POST = $request->request->all();
$FILES = $request->files->all();

// checkbox can have any value and will be successful in case "on"
// http://www.w3.org/TR/html401/interact/forms.html#checkbox
$POST['agreement'] = isset($POST['agreement']) ? 'on' : 'off';
ksort($POST);
echo html_escape_value(mink_dump($POST)) . "\n";
if (isset($FILES['about']) && file_exists($FILES['about']->getPathname())) {
    echo html_escape_value($FILES['about']->getClientOriginalName()) . "\n";
    echo html_escape_value(file_get_contents($FILES['about']->getPathname()));
} else {
    echo "no file";
}
?>
</pre>
</body>
</html>
