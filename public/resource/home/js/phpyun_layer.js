/**
 * 使2017.11.15之前对独立弹窗组件layer.min.js的调用方法兼容于layui.use(['layer'])

 * 注意：该文件必须在 layui/layui.js 文件之后引入页面（同时删除原来引用的 js/layer/layer.min.js）
*/

/*
 * 使旧的layer.msg()调用兼容layui.use(['layer'])
 *
 * msg : 消息内容
 * timeSecond : 时间，单位秒，可以有小数位
 * icon ： 1 打勾，2打叉，5/9伤心，6/8笑脸，7感叹号
 * callback : 消息展示结束后回调函数
*/
layui.use(['layer'], function(){
	var layer = layui.layer,
    $ = layui.$;

	//先保存原始的layer.msg()方法
	layer.oriMsg = layer.msg;

	//再重写layer.msg()
  // layer.msg = function (msg, timeSecond = 1.5, icon = 6, callback = function(){}){
  layer.msg = function (msg, timeSecond , icon , callback ){
    timeSecond = (typeof timeSecond !== 'undefined') ?  timeSecond : 1.5;
    icon = (typeof icon !== 'undefined') ?  icon : 6;
    callback = (typeof callback !== 'undefined') ?  callback : function(){};

  	//兼容原本layui.use(['layer'])的layer.msg()用法
  	if(typeof(timeSecond) == 'object'){
  		if(typeof(icon) == 'function'){
  			return layer.oriMsg(msg, timeSecond, icon);
  		}
  		else{
  			return layer.oriMsg(msg, timeSecond);
  		}
  	}

    var tm = timeSecond * 1000;

		//layui.use(['layer'])中icon： 1 打勾，2打叉，5伤心，6笑脸，7感叹号
		//layer.min.js中icon： 8失败，9成功
		if(icon == 8){
			icon = 5;
		}
		if(icon == 9){
			icon = 6;
		}

		return layer.oriMsg(msg,
			{
				time : tm,
				icon : icon,
                shade: [0.8, '#393D49'] //加过滤层黑色透明背景
			},
			function(){
				callback();
			}
		);
  };//end layer.msg

	//加载动画加遮罩层
	layer.oriLoad = layer.load;
	layer.load = function(icon,options)
	{
		icon = (typeof icon !== 'undefined') ? icon : 0;
		options = (typeof options == 'object') ? options : {};
		
		options.shade = [0.8, '#393D49'];
		return layer.oriLoad(icon, options);
	};

	//alert对话框
	layer.oriAlert = layer.alert;
	layer.alert = function(msg, icon, title, callback)
	{
		//layui的layer模块原本调用方式
		if(typeof icon == 'object'){
			if(typeof title == 'function'){
				return layer.oriAlert(msg, icon, title);
			}
			else{
				return layer.oriAlert(msg, icon);
			}
		}

		//兼容layer.min.js的调用方式
		if(typeof title == 'undefined'){
			return layer.msg(msg, 1.5, icon);
		}

		if(typeof callback == 'function'){
			return layer.oriAlert(msg, {title : title}, callback);
		}
	}

  /**
   * 【页面层（和父窗口属于同一个html页面）】 封装layer.open({type:1})
   *
   * content : 展示的内容 ： html代码（字符串），dom节点（例如：$("#id") ）
   * area : ['300px', '200px']
   * offset :  ['100px', '50px'] , 'auto', 'r' 等
   * options : {} 其他layui文档中的参数
  */
  // layer.page = function (content, title, area, offset = 'auto', options = {}){
  layer.page = function (content, title, area, offset , options ){
    offset = (typeof offset !== 'undefined') ?  offset : 'auto';
    options = (typeof options !== 'undefined') ?  options : {};

  	options.type = 1;
  	options.content = content;
  	options.area = area;
  	options.offset = offset;
  	options.title = title;

  	return layer.open(options);
  };

  //封装layer.open({type:2})【iframe页面层】
  // layer.iframe = function (url, title, area, offset = 'auto', options = {}){
  layer.iframe = function (url, title, area, offset, options ){//浏览器兼容写法
    offset = (typeof offset !== 'undefined') ?  offset : 'auto';
    options = (typeof options !== 'undefined') ?  options : {};

  	options.type = 2;
  	options.content = url;
  	options.area = area;
  	options.offset = offset;
  	options.title = title;

  	return layer.open(options);
  };
});//end layui.use()
function monthclick(laydate,elem,hasdone){
	var timestamp=new Date();
	nowyear = timestamp.getFullYear(),
	nowmonth = timestamp.getMonth() + 1,
	nowday = timestamp.getDate();
	laydateobj = 
	laydate.render({
		elem: elem,
		type: 'month',
		max: nowyear+'-'+nowmonth+'-'+nowday,
		change: function(value, date, endDate){
			var oldVal = $(elem).val();
			$(elem).val(value);
			if(oldVal.substr(0, 4) == value.substr(0, 4) || nowyear == value.substr(0, 4)){
			  $('.laydate-btns-confirm').click();
			}
		},
		done: function(value, date, endDate) {
			if(hasdone==1){
				var id=elem.replace('#','');
				checkonblur(id);
			}
		}
	});
}
if(typeof($) !== 'undefined'){
  $.layer = function(obj){
    var retval;
    layui.use(['layer'], function(){
      var layer = layui.layer,
        $ = layui.$;

      var offset = 'auto';
      if(obj.offset){
        offset = obj.offset;
      }

      var content = '';
      if(obj.page){
        if(obj.page.dom){
          content = $(obj.page.dom);
        }
        else if(obj.page.html){
          content = obj.page.html;
        } 
      }
      else if(obj.iframe){
        if(obj.iframe.src){
          content = obj.iframe.src;
        }
      }
      
      var id = obj.id ? obj.id : '';
      
      var close = obj.close ? obj.close : function(){};

      retval = layer.open({
        type : obj.type,
        title : obj.title,
        area : obj.area,
        content : content,
        offset : offset,
        id : id,
        end : close
      });
    });//
    
    return retval;
  };//end $.layer  
}

