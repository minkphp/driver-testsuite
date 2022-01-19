<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<html>
<body>
<?php
if ($request->isMethod('POST')) {
    $resp = new Symfony\Component\HttpFoundation\Response();
    $cook = Symfony\Component\HttpFoundation\Cookie::create('tc', $request->request->get('cookie_value'));
    $resp->headers->setCookie($cook);
} elseif ($request->query->has('show_value')) {
    echo html_escape_value($request->cookies->get('tc'));
    return;
}
?>
    <form method="post">
        <input name="cookie_value">
        <input type="submit" value="Set cookie">
    </form>
</body>
