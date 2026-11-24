<?php
require_once 'utils.php';

$file = $_FILES['the_file'] ?? null;
$uploaded = null !== $file && UPLOAD_ERR_OK === $file['error'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>File upload result</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<div id="name"><?php echo $uploaded ? html_escape_value($file['name']) : 'no file'; ?></div>
<div id="content"><?php echo $uploaded ? html_escape_value((string) file_get_contents($file['tmp_name'])) : ''; ?></div>
</body>
</html>
