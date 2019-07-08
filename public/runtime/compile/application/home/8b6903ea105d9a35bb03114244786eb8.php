<?php /* topnuomi */ (!defined('APP_PATH')) && exit(0); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/resource/jquery.min.js"></script>
</head>
<body>
    <h1>Hello</h1>    <?php $i = 0; foreach ($lists as $value): $i++;  echo $value;  endforeach; ?>
</body>
</html>