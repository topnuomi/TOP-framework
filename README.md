# TOP-Framework
*这是一个部分代码源自三年前毕业设计中的代码集合，后经过一系列重构，形成的一套框架。在此准备写一个文档。*

## 模块
### 创建模块
1. 手动创建
在application（可更改）目录下创建home目录，再创建config、controller（必须）、model、view目录
2. 命令行自动创建
命令格式（[]为可选参数）：
```
php create.php 目录 模块名 [入口文件]
```

进入framework/create/目录，执行以下命令：
```
php create.php application home index
```

至此，已经通过简单的命令创建了home模块，浏览器输入127.0.0.1即可访问home模块（系统默认模块为home模块，可在入口文件中指定），亦可命令行访问home模块。

## 控制器
### 创建控制器
一个典型的控制器（Index.php）
```
<?php

namespace app\home\controller;

class Index
{
    public function index()
    {
        return [];
    }
}
```
其中包括了命名空间、以及一个默认的index方法，index方法返回数组或布尔值true则会显示模板，如果返回的是数字或字符串则会直接输出，返回对象直接输出[OBJECT]，关于模板部分后面会详细介绍。

如果当前控制器继承自top\library\Controller基础控制器，则会继承以下方法：
1. json($msg, $code = 1, $data = [], $ext = []) 

返回json数据。

2. cache($status = true)

如果在方法中调用了此方法则会将模板做静态缓存，缓存时间在配置文件中设置。

3. param($name, $value)

将参数传递到模板文件。

4. view($file = '', $param = [], $cache = false)

显示模板（得到模板文件渲染后的内容）。

5. redirect($url)

利用header函数跳转。

6. tips($message, $url = '', $sec = 3)

如果是AJAX请求则会返回json数据（调用json方法），普通请求则返回tips模板文件渲染后的内容。
### 展示模板
```
public function index()
{
    return $this->view();
}
```
调用基础控制器中的view方法、并return出去，完成模板的展示。
### 模板传值
1. view方法
```
public function index()
{
    return $this->view(null, [
        'param' => 'Hello world!'
    ]);
}
```
2. 直接return数组
```
public function index()
{
    return [
        'param' => 'Hello world!'
    ];
}
```
以上两种方式等效。
### 控制器方法的前置、后置操作
```
public function before_index()
{
}

public function after_index()
{
}

public function index()
{
    return [
        'param' => 'Hello world!'
    ];
}
```
命名规范：before_方法名（前置）、after_方法名（后置），执行index方法之前会先执行before_index方法（如果存在），执行完index方法后会执行after_index方法（如果存在）。当前置方法return的值为空字符串、null、true时才会继续执行，否则前置方法的return等效于index方法的return。

## 模型
### 创建模型
一个典型的模型（Users.php）
```
<?php

namespace app\home\model;

use top\library\Model;

class 模型名称 extends Model
{
}
```
系统会根据模型名称去绑定对应同名称的数据表，例：模型名称为Users时，则绑定名为”前缀_users“的数据表。如果模型名称为UsersInfo时，则绑定名为“前缀_users_info”的数据表。

继承自top\library\Model基础模型后，模型将拥有以下方法或属性：
#### 方法：
1. data($data = [], $notRaw = true)
获取即将操作的数据

接收两个参数，参数一：指定的数据（数组），传入空数组则为POST数据。参数二：是否返回进行数据表字段过滤的原始数据（布尔值）。

未通过验证则返回false，否则返回数组。

2. query($query)
执行一条SQL语句

成功返回true，失败抛出DatabaseException异常。

3. insert($data = [])
插入一条记录

传入数组为即将插入的记录

成功返回受影响的记录数，失败抛出DatabaseException异常。

4. update($data, [$param = false])
更新一条记录

第一个参数为即将更新的数据，可传入第二个参数为主键。
```
$this->update([
    'username' => 'TOP糯米'
], 1);
```
除此之外，提供另一种方式，传递更多条件或更复杂的条件
```
$this->update([
    'username' => 'TOP糯米'
], function ($model) {
    $model->where('id', 1);
});
```
当然，也可以使用连贯操作
```
$this->where('id', 1)->update([
    'username' => 'TOP糯米'
]);
```

成功返回受影响的记录数，失败抛出DatabaseException异常。

5. find($param = false, $notRaw = true)
查找一条记录

可传入第一个参数为主键，第二个参数为是否按指定的规则（outReplace属性）进行处理。

一般调用
```
$this->find(1);
```
匿名函数
```
$this->find(function ($model) {
    $model->where('id', 1);
});
```
连贯操作
```
$this->where('id', 1)->find();
```

成功返回一个一维数组，失败抛出DatabaseException异常。

6. select($param = false, $notRaw = true)
查找多条记录

使用方法同find

成功返回一个二维数组，失败抛出DatabaseException异常。

7. delete
删除记录

直接传入主键
```
$this->delete(1);
```
匿名函数
```
$this->delete(function () {
    $model->where('id', 1);
});
```
连贯操作
```
$this->where('id', 1)->delete();
```

成功返回受影响的记录数，失败抛出DatabaseException异常。

8. count
返回记录数

一般调用
```
$this->count();
```
第一个参数同样可以为匿名函数、并且同样支持连贯操作

成功返回记录数，失败抛出DatabaseException异常。

9. avg
计算平均值

接收一个参数，当没有使用field方法指定字段时，可直接传入字段名，以计算平均值。
```
$this->avg('score');
```
使用field方法指定字段
```
$this->field('score')->avg();
```
匿名函数中指定字段或条件
```
$this->avg(function ($model) {
    $model->where('score', '>=', 60);
    $model->field('score');
});
```
成功返回平均值，失败抛出DatabaseException异常。

10. max
计算最大值

同avg方法

11. min
计算最小值

同avg方法

12. sum
求和

同avg方法

13. _sql
返回最后执行的SQL语句

14. tableDesc
返回表结构

接收参数为一个完整表名。

成功返回表结构，失败抛出DatabaseException异常。

15. distinct
过滤记录中的重复值

接收一个为字段名称的参数

调用
```
$this->distinct('sex')->select();
```

失败抛出DatabaseException异常。

16. effect
删除时指定表（别）名

接收一个参数，可以为字符串或数组，参数为表名或表别名

调用
```
$this->delete(function ($model) {
    $model->effect('s,this');
    $model->join('left', 'score', 's')->on('s.uid = this.id');
    $model->where(['this.id' => 3]);
});
```

17. field
指定字段

可传入字符串或数组

18. where
指定条件

最多可接收三个参数

仅传入一个参数时，可传入字符串或数组
```
$where = ['id' => ['>', 10], 'sex' => 1];
$where = 'id > 10 and sex = 1';
$this->where($where)->select();
```
两个参数，解析为“字段=值”
```
$this->where('sex', 1)->select();
```
三个参数，指定字段与字段值的连接符
```
$this->where('sex', '>', 1)->select();
```

19. order
对结果进行排序

```
$this->order('id desc')->select();
```
也可以使用匿名函数调用order方法

20. limit
查询范围

接收一个参数，可以是字符串或数组

一般调用
```
$this->limit('0, 5')->select();
$this->limit([0, 5])->select();
```
两种方式等效

21. join
加入多表进行查询，通常情况下与on方法同时使用

接收三个参数，第一个参数为连接方式（空、left、right、full），第二个参数为表名（不包含前缀），第三个参数为别名（当前表会自动将”this“作为别名）。

一般调用
```
$this->select(function ($model) {
    $model->join('left', 'score', 's')->on('s.uid = this.id');
});
```
同样也可以使用连贯操作

22. on
表字段连接关系

见join方法

#### 属性：
1. $table
指定当前模型的表名（优先于模型名称）

```
protected $table = 'users';
```

2. $pk
指定当前模型的主键（如果不指定，程序将自动查询以当前模型命名的表的主键）

```
protected $pk = 'id';
```

3. $map
指定传入数据的键与数据库字段名称的映射关系

```
protected $map = [
    'name' => 'username'
];
```

4. $inReplace
入库时替换值

数据入库时自动格式化时间为unix时间戳
```
protected $inReplace = [
    'create_time' => ['formatTime', true]
];

protected function formatTime($time)
{
    return strtotime($time);
}
```
至此，在数据在被写入数据库之前会先调用inReplace中设定的函数、方法，并将return的值作为新的值写入数据库。

注意：当以字段为键名的数组的值为一个字符串时，则该字符串为即将调用的函数，如果值为一个数组，且无第二个值或第二个值为false、空，则该数组第一个值为即将调用的函数，如第二个值为true，则表示当前调用的方法存在于本类或父类中。

5. $outReplace
出库时替换值

```
protected $outReplace = [
    'sex' => ['outFormatSex', true]
];

protected function outFormatSex($sex)
{
    switch ($sex) {
        case 1:
            return '男';
            break;
        case 2:
            return '女';
            break;
        default:
            return '未知';
    }
}
```

注意：当以字段为键名的数组的值为一个字符串时，则该字符串为即将调用的函数，如果值为一个数组，且无第二个值或第二个值为false、空，则该数组第一个值为即将调用的函数，如第二个值为true，则表示当前调用的方法存在于本类或父类中。

6. $updateReplace
数据更新时替换值

基本类似于inReplace，但仅当执行更新操作时执行。

7. $validate
自动验证

验证不为空
```
protected $validate = [
    'username' => ['notNull', '用户名不能为空'],
];
```
验证不等于
```
protected $validate = [
    'username' => ['notEqual', '0', '用户名不能为0'],
];
```
多条件（用户名不为空且不为0）
```
protected $validate = [
    'username' => [
        ['notNull', '用户名不能为空'],
        ['notEqual', '0', '用户名不能为0'],
    ],
];
```
自定义函数验证，新建函数demo到函数库
```
function demo($name, $n1, $n2)
{
    if ($name == $n1 || $name == $n2) {
        return false;
    }
    return true;
}
```
添加到自动验证
```
protected $validate = [
    'username' => [
        ['notNull', '用户名不能为空'],
        ['notEqual', '0', '用户名不能为0'],
        ['demo', 'TOP糯米', '张三', '用户名不能为TOP糯米或张三'],
    ],
];
```

### 调用模型
调用模型有两种方式：
1. model函数（推荐）
model函数会返回一个模型的单例，使用方式与直接new无差别。
```
$object = model(模型);
```
2. new 模型
```
$object = new 模型();
```

## 模板
### 模板继承
模板继承通过extend标签与block标签配合使用实现。
一个最简单的继承
```
// Base/layout.html（父级模板）

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<block name="body"></block>
</body>
</html>

// Index/index.html

<extend file="Base/layout" />
<block name="body">
    <h3>内容主体</h3>
</block>
```
### 模板标签
内置一些常用标签
1. php

php标签。此标签为闭合标签，标签内的内容将被解析为原生php代码执行。

```
<php>
    echo '你好';
</php>
```

2. if

if标签。此标签为闭合标签，condition属性为if的条件，属性列表：condition。

```
<if condition="$age eq 10">
    // do something...
</if>
```

3. else

else标签。此标签为自闭合标签，可选属性condition，存在condition属性则被解析为elseif，属性列表：condition（可选）。

```
<if condition="$age eq 10">
    // do something...
<else />
    // do something...
</if>

<if condition="$age eq 10">
    // do something...
<else condition="$age eq 20" />
    // do something...
</if>
```

4. volist

循环标签。此标签为闭合标签，属性列表：name、id、key（可选）。

```
<volist name="lists" id="item">
    {$item['id']}
</volist>

<volist name="lists" id="item" key="i">
    {$i}、{$item['id']}
</volist>
```

5. assign

赋值标签，在模板中创建新的php变量。此标签为自闭合标签，属性列表：name、value。

```
<assign name="username" value="TOP糯米" />
```

6. raw

该标签为闭合标签。raw标签中的内容不会被编译。

```
<raw>
    <volist name="lists" id="item">
        {$item['id']}
    </volist>
</raw>
```
上例，volist标签会被原样输出。

7. 变量、函数输出
```
// 变量输出
{$username}

// 调用函数，左定界符后加上:表示调用函数
{:mb_substr($username, 0, 3, 'utf8')}
```

### 自定义标签
新建自定义标签库类文件/application/home/taglib/Extend.php，目录及文件名称没有要求。
#### 闭合标签
```
namespace app\home\taglib;

class Extend
{
    public $tags = [
        'test' => ['attr' => 'start,length,id', 'close' => 1]
    ];

    public function _test($tag, $content)
    {
        $parse = '<?php ';
        $parse .= 'for ($' . $tag['id'] . ' = ' . $tag['start'] . '; $' . $tag['id'];
        $parse .=  ' < ' . $tag['start'] . ' + ' . $tag['length'] . '; ';
        $parse .= '$' . $tag['id'] . '++): ?>';
        $parse .= $content;
        $parse .= '<?php endfor; ?>';
        return $parse;
    }
}
```
类创建完成后，到配置文件config.php的view下的tagLib中添加Extend类
```
'view' => [
        'tagLib' => [
            \app\home\taglib\Extend::class
        ]
    ],
```
添加完成后即可在模板中使用
```
<test start="1" length="10" id="test">
    {$test}
</test>
```
#### 自闭合标签
添加一个描述
```
'say' => ['attr' => 'what', 'close' => 0]
```
新建_say方法
```
public function _say($tag)
{
    return "<?php echo '{$tag['what']}'; ?>";
}
```
模板调用
```
<say what="Hello world!" />
```

## 自定义路由
路由配置文件位于 application 下，文件名：route.php
现有News控制器中的detail方法
```
public function detail($id)
{
    return [
        'id' => (int) $id
    ];
}
```
假设访问地址为： http://127.0.0.3/home/news/detail/id/1.html 。
### 必须参数
添加如下规则
```
'detail' => [
    '[id]',
    'home/news/detail'
]
```
完成后，可使用 http://127.0.0.3/detail/1.html 访问到对应位置。
### 可选参数
修改detail方法
```
public function detail($id = 0)
{
    return [
        'id' => (int) $id
    ];
}
```
添加路由规则
```
'detail' => [
    '[:id]',
    'home/news/detail'
]
```
完成后，可使用 http://127.0.0.3/detail.html 访问到对应位置，如果没传递id，则使用默认值。
### 多个参数
```
'detail' => [
    '[id][:type]',
    'home/news/detail'
]
```

## 其他
### Request类
获取实例
1. instance方法获取单例
```
Request::instance();
```
2. request函数获取单例
```
request();
```

#### 供调用的方法
1. isPost

判断是否是POST请求

2. isGet

判断是否是GET请求

3. isPut

判断是否是PUT请求

4. isDelete

判断是否是DELETE请求

5. isHead

判断是否是HEAD请求

6. isPatch

判断是否是PATCH请求

7. isOptions

判断是否是OPTIONS请求

8. isAjax

判断是否是AJAX请求

9. create

创建一个HTTP请求

原型：create($url, $data = [], $header = [])

第一个参数为请求的链接，第二个参数为将要POST的数据，第三个参数为指定Header参数

10. ip

返回客户端IP地址

11. module

当前请求的模型名称

12. classname

当前请求的完整控制器名称

13. controller

当前请求的不包含命名空间的控制器名称

14. method

当前请求的方法名称

15. params

当前请求所带的参数

16. get

获取get数据

原型：get($name = '*', $except = [], $filter = 'filter')

第一个参数为将要获取的变量名称（' * ' 为全部），第二个参数为过滤的变量，第三个参数为指定的过滤函数（可以为自定义函数名称或匿名函数）。

函数名称：
```
request()->get('id', ['type'], 'filter');
```
匿名函数：
```
request()->get('id', ['type'], function ($value) {
    return (int) $value;
});
```

17. post

获取post数据

使用同get方法

18. except

指定过滤的变量

取出全部get数据，但不包括type
```
request()->except('type')->get();
```

### 面向控制器的前置、后置方法（请求拦截）
创建application/home/filter/Auth.php测试文件
```
namespace app\home\filter;

use top\middleware\ifs\MiddlewareIfs;

class Auth implements MiddlewareIfs
{
    public function before()
    {
        return '拒绝请求';
    }

    public function after($data)
    {
        // TODO: Implement after() method.
    }
}
```
创建完成后，加入配置
```
'middleware' => [
    \app\home\filter\Auth::class
],
```
现在，访问项目则会得到 ' 拒绝请求 ' 结果。仅当before方法return的值为true时，程序才会继续执行，否则return等效于控制器方法的return。