<?php /* topnuomi */ (!defined('APP_PATH')) && exit(0); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="https://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
    <h1>Hello</h1>    <?php $i = 0; foreach ($lists as $value): $i++;  echo $value->name;  endforeach; ?>
</body>
</html>