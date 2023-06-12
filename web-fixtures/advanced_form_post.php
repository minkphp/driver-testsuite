<!DOCTYPE html>
<html>
<head>
    <title>Advanced form save</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<pre>
<?php
error_reporting(0);

require_once 'utils.php';

$_POST['agreement'] = isset($_POST['agreement']) ? 'on' : 'off';
ksort($_POST);
echo html_escape_value(mink_dump($_POST)) . "\n";
if (isset($_FILES['about']) && file_exists($_FILES['about']['tmp_name'])) {
    echo html_escape_value($_FILES['about']['name']) . "\n";
    echo html_escape_value(file_get_contents($_FILES['about']['tmp_name']));
} else {
    echo "no file";
}
?>
</pre>
</body>
</html>
