# TOP-Framework
*部分代码于四年前所写，后经过一系列重构，形成的一套框架。在此准备写一个文档。*

## 目录结构
遵循PSR-2规范的编码风格，遵循PSR-4自动加载规范。
基本目录结构：
```
-application        应用目录
-framework          框架所在目录
    -config           默认配置文件目录
    -create           自动生成模块
    -extend           功能扩展类目录
    -library          核心类库目录
        -cache          缓存具体实现
        -database       数据库操作具体实现
        -error          错误处理
        -exception      异常处理
        -functions      框架函数库
        -http           请求/响应类
        -template       模板引擎具体实现
        -......         实际调用的类
    -middleware        默认中间件
    -traits            通用trait
    -vendor            composer加载的类库
-public              可访问公共资源
```

## 入口文件
### 入口文件中的配置
```
use \top\Framework;

require '../framework/Framework.php';

// 可能你会使用到下面这些配置

// 调试模式，缺省值：false
// Framework::debug(true);
// 可使用常量DEBUG取得该值

// 项目目录，无缺省值，必须指定
// Framework::appPath('../application/');
// 可使用常量APP_PATH取得该值

// 项目命名空间，缺省值：app
// Framework::appNameSpace('app');
// 可使用常量APP_NS取得该值

// session保存目录，缺省值：./runtime/session/
// Framework::sessionPath('./runtime/session/');
// 可使用常量SESSION_PATH取得该值

// 框架目录，缺省值：Framework.php的绝对路径
// Framework::frameworkPath('../framework');
// 可使用常量FRAMEWORK_PATH取得该值

// 静态资源目录，缺省值：/resource/
// Framework::resourcePath('/resource/');
// 可使用常量RESOURCE取得该值

// 绑定模块，缺省值：home
// Framework::bindModule('index');
// 可使用常量BIND_MODULE取得该值

Framework::appPath('../application/');
Framework::startApp();
```

## 模块
### 创建模块
1. 手动创建
在application（可更改）目录下创建home目录，再创建config、controller（必须）、model、view目录
2. 命令行自动创建
```
php create.php 目录 模块名 [入口文件]
```

进入framework/create/目录，执行以下命令：
```
php create.php application home index
```

至此，已经通过简单的命令创建了home模块，浏览器输入127.0.0.1即可访问home模块（系统默认模块为home模块，可在入口文件中使用Framework::bindModule()指定）。

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
此外，控制器方法中可以使用view函数完成相同操作。

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
1. view方法/view函数
```
public function index()
{
    return $this->view(null, [
        'param' => 'Hello world!'
    ]);
    return view(null, [
        'param' => 'Hello world!'
    ]);
}
```
2. view_param函数
```
public function index()
{
    view_param([
        'param' => 'Hello world!',
    ]);
    return $this->view();
}
```
3. 直接return数组
```
public function index()
{
    return [
        'param' => 'Hello world!'
    ];
}
```
以上两种方式等效。

### 参数自动注入
```
namespace app\home\controller;

use app\home\model\Users;
use top\library\http\Request;

class Index
{

    protected $users = null;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }
    
    public function index(Request $request, Users $users)
    {
        return 'Hello';
    }
}
```
可在参数中指定所有可以被实例化（外部new或外部静态调用instance方法）的类，将自动将类实例化并注入到方法中。

### 前置与后置操作
前、后置操作仅支持使用注解配置，多个方法可使用|隔开。方法名称必须以_开头，如果前置方法return的值为真，则不会继续执行。
```
/**
 * @beforeAction _before
 * @afterAction _after
 */
public function index()
{
    return 'Hello';
}

public function _before()
{
    // 前置操作
}

public function _after()
{
    // 后置操作
}
```
这部分的实现代码是放在默认Action中间件中的，后面可能会改。

## 模板
框架自带一款模板引擎，暂时命名为TOP模板引擎。此外支持扩展其他第三方模板引擎，后面会讲到，先来看看自带模板引擎的基础使用。
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

4. loop

循环标签。此标签为闭合标签，属性列表：name、id、key（可选）。

```
<loop name="lists" id="item">
    {item.id}
</loop>

<loop name="lists" id="item" key="i">
    {i}、{item.id}
</loop>
```

5. assign

赋值标签，在模板中创建新的php变量。此标签为自闭合标签，属性列表：name、value。

```
<assign name="username" value="TOP糯米" />
```

6. original

此标签为闭合标签。original标签中的内容不会被编译。

```
<original>
    <loop name="lists" id="item">
        {item.id}
    </loop>
</original>
```
上例，loop标签会被原样输出。

7. switch

此标签为闭合标签。

```
<assign name="value" value="1" />
<switch name="value">
    <case value="0">
        $name的值为0
    </case>
    <case value="1">
        $name的值为1
    </case>
    <case>默认</case>
</switch>
```

8. include

在当前模板中加载其他模板文件。

9. 变量、函数调用
```
// 变量输出
{username}

// 函数调用需要在左定界符后加上:
{:mb_substr($username, 0, 3, 'utf8')}
```

### 自定义标签
新建自定义标签库类文件/application/home/tags/Extend.php，目录及文件名称没有要求。自定义标签会以类名进行分组。
#### 闭合标签
```
namespace app\home\tags;

class Extend
{
    public $tags = [
        'test' => ['attr' => 'start,length,id', 'close' => 1]
    ];

    public function _test($attr, $content)
    {
        $parse = '<?php for ($' . $attr['id'] . ' = ' . $attr['start'] . '; $' . $attr['id'];
        $parse .=  ' < ' . $attr['start'] . ' + ' . $attr['length'] . '; ';
        $parse .= '$' . $attr['id'] . '++): ?>';
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
        \app\home\tags\Extend::class
    ]
],
```
添加完成后即可在模板中使用
```
<extend:test start="1" length="10" id="test">
    {$test}
</extend:test>
```
#### 自闭合标签
添加一个描述
```
'say' => ['attr' => 'what', 'close' => 0]
```
新建_say方法
```
public function _say($attr)
{
    return "<?php echo '{$attr['what']}'; ?>";
}
```
模板调用
```
<extend:say what="Hello world!" />
```

### 模板缓存
#### 实现
在渲染模板后，可选择将渲染结果缓存下来（文件缓存）。在框架调用控制器之前有面向控制器的前置操作，在此会判断是否存在模板缓存文件，如果存在并且有效，则会直接使用缓存文件。否则，将会重新渲染模板。
#### 使用
1. 在view方法中使用。第三个参数为缓存控制，传入的参数为true时使用配置文件中设置的全局缓存时间，传入数字则表示当前模板的缓存时间（秒）
```
return $this->view('Index/index', [
    'data' => $data
], 30);
```
2. 直接在控制器中调用cache方法。参数作用参照view方法。
```
$this->cache(30);
return [
    'data' => $data
];
```

### 第三方模板引擎
文件存放位置 'framework/library/template/driver' 。必须实现TemplateIfs接口，所以需要实现以下三个方法：
1. run

返回当前类实例，做模板引擎初始化操作。

2. cache

设置缓存状态。

3. fetch

返回模板渲染后的内容。

驱动类编写完成后需要以下两个步骤，方可使用（以自带模板引擎的配置为例）：
1. 配置文件中注册

```
'register' => [
    'Top' => \top\library\template\driver\Top::class,
],
```

2. 模板配置中配置使用

```
'view' => [
    'engine' => 'Top',
],
```

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
#### 方法

1. query($query)
执行一条SQL语句

成功返回true，失败抛出DatabaseException异常。

2. insert($data = [])
插入一条记录

传入数组为即将插入的记录

成功返回受影响的记录数，失败抛出DatabaseException异常。

3. update($data, $param = false)
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
], function ($db) {
    $db->where('id', 1);
});
```
当然，也可以使用连贯操作
```
$this->where('id', 1)->update([
    'username' => 'TOP糯米'
]);
```

成功返回受影响的记录数，失败抛出DatabaseException异常。

4. find($param = false, $notRaw = true)
查找一条记录，方法get可使用静态调用

可传入第一个参数为主键，第二个参数为是否按指定的规则（outReplace属性）进行处理。

一般调用
```
$this->find(1);
```
匿名函数
```
$this->find(function ($db) {
    $db->where('id', 1);
});
```
连贯操作
```
$this->where('id', 1)->find();
```

成功返回一个一维数组，失败抛出DatabaseException异常。

5. select($param = false, $notRaw = true)
查找多条记录，方法all可使用静态调用

使用方法同find

成功返回一个二维数组，失败抛出DatabaseException异常。

6. delete
删除记录

直接传入主键
```
$this->delete(1);
```
匿名函数
```
$this->delete(function ($db) {
    $db->where('id', 1);
});
```
连贯操作
```
$this->where('id', 1)->delete();
```

成功返回受影响的记录数，失败抛出DatabaseException异常。

7. count
返回记录数

一般调用
```
$this->count();
```
第一个参数同样可以为匿名函数、并且同样支持连贯操作

成功返回记录数，失败抛出DatabaseException异常。

8. avg
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
$this->avg(function ($db) {
    $db->where('score', '>=', 60);
    $db->field('score');
});
```
成功返回平均值，失败抛出DatabaseException异常。

9. max
计算最大值

同avg方法

10. min
计算最小值

同avg方法

11. sum
求和

同avg方法

12. sql
返回最后执行的SQL语句，可使用静态调用

13. distinct
过滤记录中的重复值，可使用静态调用

接收一个为字段名称的参数

调用
```
$this->distinct('sex')->select();
```

失败抛出DatabaseException异常。

14. field
指定字段，可使用静态调用

可传入字符串或数组

15. where
指定条件，可使用静态调用

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

16. order
对结果进行排序，可使用静态调用

```
$this->order('id desc')->select();
```
也可以使用匿名函数调用order方法

17. limit
查询范围，可使用静态调用

接收一个参数，可以是字符串或数组

一般调用
```
$this->limit('0, 5')->select();
$this->limit([0, 5])->select();
```
两种方式等效

18. alias
指定当前表别名，可使用静态调用

具体请看join演示

19. join
加入多表进行查询，可使用静态调用

接收三个参数，第一个参数为连接方式（空、left、right、full），第二个参数为表名（不包含前缀），第三个参数为别名（当前表会自动将”this“作为别名）。

一般调用
```
$this->alias('t')->select(function ($db) {
    $db->join('score s', 's.uid = t.id', 'left');
});
```
同样也可以使用连贯操作

20. transaction

事务处理

```
use app\home\model\Users;

$userModel = model(Users::class);
$res = $userModel->transaction(function ($db) {
    $db->delete(4);
    $db->update([
        'id' => 3,
    ], 1);
});
var_dump($res);
```
上例中，开启了一个事务，先删除一条记录，再更新一条记录的ID为已存在的ID，更新操作必定不会执行成功，所以数据将会执行回滚，删除数据也不会执行成功，var_dump($res)结果为false。返回值为布尔值，成功返回true，失败返回false。transaction方法接收一个匿名函数，匿名函数形参为当前模型。SQL执行失败时会回滚事务，也可以通过手动抛出DatabaseException异常来回滚事务。

21. getPDO
获取PDO对象

使用该方法获取到PDO对象，可以自行使用PDO操作数据库

#### 属性
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

4. $prefix
指定当前表前缀

```
protected $prefix = 'cms_';
```

5. $insertReplace
入库时替换值

数据入库时自动格式化时间为unix时间戳
```
protected $insertReplace = [
    'create_time' => ['formatTime', true]
];

protected function formatTime($time)
{
    return strtotime($time);
}
```
至此，在数据在被写入数据库之前会先调用inReplace中设定的函数、方法，并将return的值作为新的值写入数据库。

注意：当以字段为键名的数组的值为一个字符串时，则该字符串为即将调用的函数，如果值为一个数组，且无第二个值或第二个值为false、空，则该数组第一个值为即将调用的函数，如第二个值为true，则表示当前调用的方法存在于本类或父类中。

6. $outReplace
出库时替换值

```
protected $outReplace = [
    'sex' => ['outFormatSex', true]
];

protected function outFormatSex($sex)
{
    switch ($sex) {
        case 1: return '男';
        case 2: return '女';
        default: return '未知';
    }
}
```

注意：当以字段为键名的数组的值为一个字符串时，则该字符串为即将调用的函数，如果值为一个数组，且无第二个值或第二个值为false、空，则该数组第一个值为即将调用的函数，如第二个值为true，则表示当前调用的方法存在于本类或父类中。

7. $updateReplace
数据更新时替换值

基本类似于inReplace，但仅当执行更新操作时执行。

8. $validate
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

## 缓存
缓存类必须实现CacheIfs接口，所以需要实现以下四个方法：
1. set

设置缓存。三个参数，前两个为必须，第三个默认为10（秒），第一个参数为准备缓存数据的key，第二个参数为缓存的具体数据（字符串、数组、对象），第三个为当前缓存的有效时间。
```
$cache->set('lists', [0, 1, 2, 3, 4, 5], 30);
```

2. get

根据key获取缓存内容
```
$cache->get('lists');
```

第一个参数为缓存标识，第二个可选参数为当前缓存不存在即将调用的匿名函数，并将返回值当作第一次调用的缓存值。

3. remove

根据key删除缓存
```
$cache->remove('lists');
```

4. exists

根据key判断缓存是否存在/有效
```
$cache->exists('lists');
```

### File
1. 使用判断设置缓存
```
use top\library\cache\driver\File;

$cache = File::instance();
if (!$cache->exists('text')) {
    $text = '测试';
    $cache->set('text', $text);
}
$data = $cache->get('text');
```
2. get方法
```
use top\library\cache\driver\File;

$cache = File::instance();
$data = $cache->get('text', function ($cache) {
    $cache->set('text', $text = '测试');
    return $text;
});
```
### Redis

使用方式同File缓存

### 自定义缓存实现
文件存放位置 'framework/library/cache/driver' 。必须实现CacheIfs接口，具体方法看缓存介绍。

## 路由
可在config.php配置文件中使用的配置
```
'compel_route' => false, // 是否开启强制路由
'complete_parameter' => true, // 是否开启完整参数
```

### 默认路由
访问方式 http://yourdomain/控制器/方法 
开启完整参数，则传递参数需要使用完整名称，如方法有参数id，则需要将id在链接中体现
http://yourdomain/Users/edit/id/1
关闭完整参数，可以直接跟参数值
http://yourdomain/Users/edit/1

### 强制路由
开启强制路由后，默认路由方式将全部失效，仅手动配置（配置文件、注解）的路由可用。

### 路由配置
#### 方法注解
假设有控制器Index
```
class Index
{
    /**
     * 首页
     * @route /home
     * @requestMethod GET
     */
    public function index()
    {
      return 'Hello';  
    }
}
```
给index方法添加注解@route为/home，则可以直接通过 http://yourdomain/home 访问到对应位置，@requestMethod注解为请求方法，如果不指定请求方法，则任何方法都可访问。

#### 配置文件
路由配置文件位于模块config目录下，文件名：route.php
```
return [
    'get' => [
        '/home' => [
            'class' => app\home\controller\Index::class,
            'method' => 'index',
        ],
    ],
];
```
进行以上配置后，可以通过GET方式请求 http://yourdomain/home 访问到对应位置。可配置的组有any、其他请求方法，其中any为不限制，任何方法都可访问，路由配置文件中可以指定执行、不执行的中间件，accept_middleware为执行的中间件，except_middleware为不执行的中间件，如下：
```
return [
    'get' => [
        '/home' => [
            'class' => app\home\controller\Index::class,
            'method' => 'index',
            'accept_middleware' => [
                app\home\middleware\Auth::class    
            ],
            'except_middleware' => [],
        ],
    ],
];
```

## 其他
### Database类
模型中的数据库操作实际也是调用Database类中的方法，模型类是在此基础上新增了更多高级操作，Database类方法的使用请参照模型的使用。在此需要特别指出，获取Database类实例使用table方法，table方法中传入表名以指定即将操作的数据表，例：
```
$db = Database::table('users');
$data = $db->find(1);
```
框架使用PDO操作数据库。此外，支持自定义数据库操作驱动类，文件位置 'framework/library/database/driver' ，必须实现连接数据库、获取表主键方法，可以继承Base类，拥有通用的增删改查方法。此外，可以实现DatabaseIfs接口，包括以下方法：
1. connect

使用PDO连接数据库。

2. getPk

获取表主键。

#### 注意
Database类仅提供基本的增删改查，不会有模型中的入库、出库值替换，也不会有数据验证等操作，查询使用方式与Model类基本相同。Database类的事务与Model类不同，Model类进行了更进一步的封装。Database类事务使用示例：
```
use top\library\Database;

$db = Database::table('users');
// 开启事务
$db->begin();
try {
    // 一些对数据库的改动操作

    // 提交
    $db->commit();
} catch (DatabaseException $exception) {
    // 回滚
    $db->rollback();

    // 其他操作
}
```

### Request类
#### 获取实例
```
Request::instance();
// 或者
request();
```
#### 方法
1. is
判断请求方法
```
// 判断是否为POST请求
request()->is('post');
// 判断是否为GET请求
request()->is('get');
```

2. create

创建一个HTTP请求

原型：create($url, $data = [], $header = [])

第一个参数为请求的链接，第二个参数为将要POST的数据，第三个参数为指定Header参数

3. ip

返回客户端IP地址

4. module

当前请求的模型名称

5. controllerFullName

当前请求的完整控制器名称

6. controller

当前请求的不包含命名空间的控制器名称

7. method

当前请求的方法名称

8. params

当前请求所带的参数

9. get

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

10. post

获取post数据

使用同get方法

11. header

获取请求中header数据

使用同get方法

12. except

指定过滤的变量

取出全部get数据，但不包括type
```
request()->except('type')->get();
```

### Response类
#### 获取实例
```
Response::instance();
// 或者
response();
```
#### 响应内容
```
return $response->code(200)->header([
    'X-Powered-By: 测试',
])->send('OK');
```
其中，header方法接收数组或字符串的参数，参数为具体的响应头内容。send方法参数为具体响应内容。

### 中间件
### 创建中间件
创建application/home/middleware/Auth.php测试文件
```
namespace app\home\middleware;

use top\middleware\ifs\MiddlewareIfs;
use top\library\http\Request;

class Auth implements MiddlewareIfs
{
    public function handler(Request $request, \Closure $next)
    {
        if (true) {
            return '拒绝请求';
        }
        return $next();
    }
}
```
#### 使之生效
##### 方法注解
```
/**
 * @acceptMiddleware app\home\middleware\Auth
 */
public function index()
{
}
```

##### 配置文件
```
'middleware' => [
    app\home\middleware\Auth::class
],
```

配置文件中的配置的中间件是全局有效的。使用注解，可以为方法指定执行的中间件，同样也可以为方法指定不执行的中间件，只需要加上@exceptMiddleware注解即可，如下：
```
/**
 * @exceptMiddleware app\home\middleware\Auth
 */
public function index()
{
}
```

### config.php
以home模块为例，文件位置 'application/home/config/config.php'。此外还存在一个默认配置文件，文件位置 'framework/config/config.php'，如果用户存在同名配置，将会执行merge操作。
如果需要配置数据库，将如下内容添加到应用配置文件中
```
'db' => [
    'driver' => 'Mysql',
    'host' => '127.0.0.1',
    'user' => '',
    'passwd' => '',
    'dbname' => '',
    'prefix' => '',
    'port' => 3306,
    'charset' => 'utf8'
],
```
如果需要其他配置，请到framework/config/config.php中查看其他的配置。

### Config类
Config类用于获取、设置配置。
#### 获取实例
```
Config::instance();
```
#### 动态配置
```
$instance->set('appid', '1234566');
```
#### 获取配置
```
$instance->get('appid');
```

### Composer
请配置vendor目录到framework/vendor。

