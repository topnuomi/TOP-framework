<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
父级模板

    BODY
    <?php echo $num;  $b = 1; ?>
    
        <volist name="b" id="vo">
            <if condition="$a == 1">
                {$a}
            <else condition="$a == 2" />
                {$b}
            </if>
        </volist>
    
    a.html
b.html

</body>
</html>