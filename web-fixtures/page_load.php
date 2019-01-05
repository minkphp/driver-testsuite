<?php

$sleep = isset($_GET['sleep']) ? $_GET['sleep'] : 0;
sleep($sleep);
echo 'success';