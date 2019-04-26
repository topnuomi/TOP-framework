/**
 * 省市县三级联动select表单
 * 
 *
*/
layui.use(['form'], function(){
  var $ = layui.$,
  form = layui.form;  

  form.on('select(citys)', function(data){
    var html = "<option value=''>请选择</option>";
    if(data.value){
        $.each(ct[data.value], function(k, v){
            html += "<option value='" + v + "'>" + cn[v] + "</option>";
          });
    }
    if(data.elem.name=='provinceid'){
      $("#cityid").html(html);
      $("#three_cityid").html("<option value=''>请选择</option>");
    }else{
      $("#cityshowth").show();
      $("#three_cityid").html(html);
    }
    form.render('select');
  });
});