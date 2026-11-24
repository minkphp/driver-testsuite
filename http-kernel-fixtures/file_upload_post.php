<?php
/** @var \Symfony\Component\HttpFoundation\Request $request */
$file = $request->files->get('the_file');
$uploaded = $file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile && $file->isValid();
?>
<!DOCTYPE html>
<html>
<head>
    <title>File upload result</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<body>
<div id="name"><?php echo $uploaded ? html_escape_value($file->getClientOriginalName()) : 'no file'; ?></div>
<div id="content"><?php echo $uploaded ? html_escape_value((string) file_get_contents($file->getPathname())) : ''; ?></div>
</body>
</html>
