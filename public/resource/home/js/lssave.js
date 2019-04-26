//保存个人用户资料临时数据
function saveuserform(){
	var savetype=1;
	var name=$.trim($("#name").val());
	var nametype=document.MyForm.nametypecid.value;
 	var sex=$("input[name='sex']:checked").val();
	var birthday=$("#birthday").val();
	var edu=document.MyForm.educid.value;
	var exp=document.MyForm.expid.value;

	var telphone=$("#telphone").val();
	var living=$("#living").val();

	var email=$("#email").val();
	var address=$("#address").val();
	var height=$("#height").val();
	var weight=$("#weight").val();
	var nationality=$("#nationality").val();
	
	var marriage=document.MyForm.marriage.value;

	var domicile=$("#domicile").val();
	var qq=$("#qq").val(); 	
	var homepage=$("#homepage").val();
 
	if(name||sex||birthday||edu||exp||telphone||living||email||address||height||weight||nationality||marriage||domicile||qq||homepage||nametype){
		$.post(weburl+"/member/index.php?m=ajax&c=saveform",{savetype:savetype,name:name,nametype:nametype,sex:sex,birthday:birthday,edu:edu,exp:exp,telphone:telphone,living:living,
			email:email,address:address,height:height,weight:weight,nationality:nationality,marriage:marriage,domicile:domicile,qq:qq,homepage:homepage},function(data){
			if(data=="1"){
				return false;
			}else{
				return false;
			}
		}); 
	}
}

//保存用户简历临时数据
function saveexpform(){
	var savetype=2;
	var uname=$("#uname").val();
	var sex=$("#sex").val();
	var birthday=$("#birthday").val();
	var living=$("#living").val();
	var edu=document.MyForm.educid.value;
	var exp=document.MyForm.expid.value;
	var telphone=$("#telphone").val();
	var email=$("#email").val();

	var hy=document.MyForm.hyid.value;
	var job_classid=$("input[name=job_class ]").val();
	var name=$("#name").val();
	var minsalary=$("#minsalary").val();
	var maxsalary=$("#maxsalary").val();
	var city_class=$("#city_class").val();
 	  
	var type=document.MyForm.typeid.value;
	var report=document.MyForm.reportid.value;
	var status=document.MyForm.statusid.value;
	
	/*最近一份工作*/
	var workname=$("#workname").val();
	var worktitle=$("#worktitle").val();
	var worksdate=$("#worksdate").val();
	var workedate=$("#workedate").val();
	var stopdate=$("input[name='stopdate']:checked").val();
	var workcontent=$("#workcontent").val(); 

	/*毕业院校*/
	var eduname=$("#eduname").val();
	var edusdate=$("#edusdate").val();
	var eduedate=$("#eduedate").val();
	var education=$("#education").val();
	//var education =document.MyForm.education.value;
	var eduspec=$("#eduspec").val();
	var edutitle=$("#edutitle").val(); 
	/*近期项目*/
	var proname=$("#proname").val();
	var protitle=$("#protitle").val();
	var prosdate=$("#prosdate").val();
	var proedate=$("#proedate").val();
	var procontent=$("#procontent").val(); 

	if(uname||sex||birthday||living||edu||exp||telphone||email||hy||job_classid||name||minsalary||maxsalary||city_class||type||report||status){
		$.post(weburl+"/member/index.php?m=ajax&c=saveform",{savetype:savetype,uname:uname,sex:sex,birthday:birthday,living:living,edu:edu,exp:exp,telphone:telphone,email:email,
			hy:hy,job_classid:job_classid,name:name,minsalary:minsalary,maxsalary:maxsalary,city_class:city_class,
			type:type,report:report,status:status,
			workname:workname,worktitle:worktitle,worksdate:workedate,stopdate:stopdate,workcontent:workcontent,
			eduname:eduname,edusdate:edusdate,eduedate:eduedate,education:education,eduspec:eduspec,edutitle:edutitle,
			proname:proname,protitle:protitle,prosdate:prosdate,proedate:proedate,procontent:procontent},function(data){
			return false;
		});
	}
}

//保存公司资料临时数据
function savecomform(){
	var savetype=3;
	var name=$("#name").val();
	var shortname=$("#shortname").val();
	
	var hy=document.myform.hy.value;
	var pr=document.myform.pr.value;
	var mun=document.myform.mun.value;

	var provinceid=document.myform.provinceid.value;
	var cityid=document.myform.cityid.value;
	var three_cityid=document.myform.three_cityid.value;
	var address=$("#address").val();
		
	var linkman=$("#linkman").val();
	var linktel=$("#linktel").val();
	
	var phoneone=$("#phoneone").val();
	var phonetwo=$("#phonetwo").val();
	var phonethree=$("#phonethree").val();

	var linkmail=$("#linkmail").val();

	var content=editor.getContent(); 

	var sdate=$("#sdate").val();
	
	var moneytype=document.myform.moneytype.value;
	var money=$("#money").val();
	var linkjob=$("#linkjob").val();
	var linkqq=$("#linkqq").val();
	var website=$("#website").val();
	var busstops=$("#busstops").val(); 

	var infostatus=document.myform.infostatus.value;

	//var welfare = $("input[name='welfare[]']:checked").serialize();
	obj = $("input[name='welfare[]']:checked");
    var welfare = [];
    for(k in obj){
        if(obj[k].checked)
            welfare.push(obj[k].value);
    }
	if(name||hy||mun||pr||address||provinceid||linkman||linktel||linkmail||content||busstops||sdate||linkqq||linkjob||website||shortname){
		$.post(weburl+"/member/index.php?m=ajax&c=saveform",{savetype:savetype,name:name,shortname:shortname,hy:hy,pr:pr,mun:mun,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,address:address,
			linkman:linkman,linktel:linktel,phoneone:phoneone,phonetwo:phonetwo,phonethree:phonethree,linkmail:linkmail,content:content,sdate:sdate
			,moneytype:moneytype,money:money,linkjob:linkjob,linkqq:linkqq,website:website,busstops:busstops,infostatus:infostatus,welfare:welfare},function(data){
			return false;
		});
	}
}

//保存发布职位临时数据
function savejobform(){
	var savetype=4;
	var name=$("#name").val();
 	var job_classid=$("input[name=job_post]").val();

	var provinceid=$("#provinceid").val();
	var cityid=$("#cityid").val();
	var three_cityid=$("#three_cityid").val();
		
	var salary_type=$("input[name='salary_type']:checked").val();
	var minsalary=$("#minsalary").val();
	var maxsalary=$("#maxsalary").val();

	var description=editor.getContent();

	var hy=document.MyForm.hy.value;
	var number=document.MyForm.number.value;
	var exp=document.MyForm.exp.value;
	var report=document.MyForm.report.value;
	var age=document.MyForm.age.value;
	var sex=document.MyForm.sex.value;
	var edu=document.MyForm.edu.value;

	var is_graduate=$("input[name='is_graduate']:checked").val();
	var marriage=document.MyForm.marriage.value;
	var lang = $("input[name='lang[]']:checked").serialize();
	
	var is_link= $("#islink").val()
	var link_man=$("#link_man").val();
	var link_moblie=$("#link_moblie").val();
	var tblink= $("#tblink").val()

	var is_email =$("#isemail").val()
	var email=$("#email").val();

	if(name||job_classid||provinceid||salary_type||minsalary||maxsalary||description||hy||number||exp||report||age||sex||edu||lang||tblink){
		$.post(weburl+"/member/index.php?m=ajax&c=saveform",{name:name,savetype:savetype,job_classid:job_classid,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,minsalary:minsalary,maxsalary:maxsalary,salary_type:salary_type,description:description,
			hy:hy,number:number,exp:exp,report:report,age:age,sex:sex,edu:edu,is_graduate:is_graduate,marriage:marriage,lang:lang,
			is_link:is_link,link_man:link_man,link_moblie:link_moblie,tblink:tblink,is_email:is_email,email:email},function(data){
			return false;
		});
	}
}

//保存发布兼职临时数据
function savepartform(){
	var savetype=5;
	var name=$("#name").val();
	var type=document.MyForm.type.value;
	var number=$("#number").val();
	
	var worktime = $("input[name='worktime[]']:checked").serialize();
	var ckAll=$("input[name='ckAll']:checked").val();
	
	var sdate=$("#sdate").val();
	var edate=$("#edate").val();
	var timetype=$("input[name='timetype']:checked").val();
	var deadline=$("#deadline").val();
	
	var salary=$("#salary").val();
	var salary_type=document.MyForm.salary_type.value;

	var billing_cycle=document.MyForm.billing_cycle.value;

	var content=editor.getContent();

	var sex=document.MyForm.sex.value;

	var provinceid=$("#provinceid").val();
	var cityid=$("#cityid").val();
	var three_cityid=$("#three_cityid").val();
	var address=$("#address").val();
	var x=$("#map_x").val();
	var y=$("#map_y").val();
	var linkman=$("#linkman").val();
	var linktel=$("#linktel").val();

   	if(name||type||number||provinceid||address||content||billing_cycle||edate||sdate||sex||worktime||ckall||salary||deadline||linkman||linktel||x||y){
		$.post(weburl+"/member/index.php?m=ajax&c=saveform",{
			savetype:savetype,name:name,type:type,number:number,worktime:worktime,ckAll:ckAll,sdate:sdate,edate:edate,timetype:timetype,deadline:deadline,
				salary_type:salary_type,salary:salary,
				billing_cycle:billing_cycle,
				provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,address:address,content:content,sex:sex,linkman:linkman,linktel:linktel,x:x,y:y},function(data){
			return false;
		});
	}
}


//读取个人用户资料临时数据
function saveuser(){
	var savetype=1;
	parent.layer.confirm("此操作将会覆盖所填写的内容？",function(){
		$.post(weburl+"/member/index.php?m=ajax&c=readform",{savetype:savetype},function(data){ 
			var data=eval('('+data+')');
			$("#name").val(data.name);
			if(data.nametype){
				$('select[name="nametype"]').next().find('.layui-anim').children('dd[lay-value="'+data.nametype+'"]').click();
			}
			if(data.sex){
				$("input[name=sex][value=1]").attr("checked", data.sex == 1 ? true : false)
				$("input[name=sex][value=2]").attr("checked", data.sex == 2 ? true : false)
			}
			$("#birthday").val(data.birthday);

 			if(data.exp){
				$('select[name="exp"]').next().find('.layui-anim').children('dd[lay-value="'+data.exp+'"]').click();
			}
			if(data.edu){
				$('select[name="edu"]').next().find('.layui-anim').children('dd[lay-value="'+data.edu+'"]').click();
			}
			$("#telphone").val(data.telphone);
			$("#living").val(data.living);

			$("#email").val(data.email);
			$("#address").val(data.address);
			$("#height").val(data.height);
			$("#weight").val(data.weight);
			$("#nationality").val(data.nationality);

			if(data.marriage){
				$('select[name="marriage"]').next().find('.layui-anim').children('dd[lay-value="'+data.marriage+'"]').click();
			}
			$("#domicile").val(data.domicile);
			$("#qq").val(data.qq);
			$("#homepage").val(data.homepage);

			layui.use('form', function() {
				var form = layui.form;  
				form.render();
			});
	  
		});
		parent.layer.closeAll();
	});
}
//读取用户简历临时数据
function saveexp(){
	var savetype=2;
	parent.layer.confirm("此操作将会覆盖所填写的内容？",function(){
		$.post(weburl+"/member/index.php?m=ajax&c=readform",{savetype:savetype},function(data){
			var data=eval('('+data+')');
			$("#uname").val(data.uname);
			if(data.sex){
				$("#sex" + data.sex).addClass('news_expect_sex_cur');
				$("#sex").val(data.sex);
  			}
			$("#birthday").val(data.birthday);
			$("#living").val(data.living);
			if(data.edu){
				$('select[name="edu"]').next().find('.layui-anim').children('dd[lay-value="'+data.edu+'"]').click();
			}
			if(data.exp){
				$('select[name="exp"]').next().find('.layui-anim').children('dd[lay-value="'+data.exp+'"]').click();
			}
			$("#telphone").val(data.telphone);
			$("#email").val(data.email);

			if(data.hy){
				$('select[name="hy"]').next().find('.layui-anim').children('dd[lay-value="'+data.hy+'"]').click();
			}
			if(data.job_classid){
				$("#job_class").val(data.job_classid);
				$("#workadds_job").val(data.job_class);
			}

			$("#name").val(data.name);
			$("#minsalary").val(data.minsalary);
			$("#maxsalary").val(data.maxsalary);
			
			if(data.city_class){
				$("#cityadds_job").val(data.city_classname);
				$("#city_class").val(data.city_class);

			}

			if(data.type){
				$('select[name="type"]').next().find('.layui-anim').children('dd[lay-value="'+data.type+'"]').click();
			}
			if(data.report){
				$('select[name="report"]').next().find('.layui-anim').children('dd[lay-value="'+data.report+'"]').click();
			}
			if(data.status){
				$('select[name="jobstatus"]').next().find('.layui-anim').children('dd[lay-value="'+data.status+'"]').click();
			}

			if(data.workname){$("#workname").val(data.workname);}
			if(data.worktitle){$("#worktitle").val(data.worktitle);}
			if(data.worksdate){$("#worksdate").val(data.worksdate);}
			if(data.stopdate=='1'){
				$("input[name='stopdate']").attr("checked",true);
 				$("#workedate").attr("disabled","disabled");
 				$("#workedate").val('');
			}else{
				$("input[name='stopdate']").attr("checked",false);
				if(data.workedate){$("#workedate").val(data.workedate);}
			}
 			if(data.workcontent){$("#workcontent").val(data.workcontent);}

			if(data.eduname){$("#eduname").val(data.eduname);}
 			if(data.edusdate){$("#edusdate").val(data.edusdate);}
 			if(data.eduedate){$("#eduedate").val(data.eduedate);}
			if(data.education){
 				$('select[name="education"]').next().find('.layui-anim').children('dd[lay-value="'+data.education+'"]').click();
			}
 			if(data.eduspec){$("#eduspec").val(data.eduspec);}
 			if(data.edutitle){$("#edutitle").val(data.edutitle);}

			if(data.proname){$("#proname").val(data.proname);}
			if(data.protitle){$("#protitle").val(data.protitle);}
			if(data.prosdate){$("#prosdate").val(data.prosdate);}
  			if(data.proedate){$("#proedate").val(data.proedate);}
 			if(data.procontent){$("#procontent").val(data.procontent);}

			layui.use('form', function() {
				var form = layui.form;  
				form.render();
			});

	});parent.layer.closeAll();});
}
//读取公司资料临时数据
function savecom(){
	var savetype=3;
	parent.layer.confirm("此操作将会覆盖所填写的内容？",function(){
		$.post(weburl+"/member/index.php?m=ajax&c=readform",{savetype:savetype},function(data){ 
			var data=eval('('+data+')');
			$("#name").val(data.name);
			if(data.shortname){
				$("#shortname").val(data.shortname);
			}
			if(data.hy){
				$('select[name="hy"]').next().find('.layui-anim').children('dd[lay-value="'+data.hy+'"]').click();
			}
			if(data.pr){
				$('select[name="pr"]').next().find('.layui-anim').children('dd[lay-value="'+data.pr+'"]').click();
			}
			if(data.mun){
				$('select[name="mun"]').next().find('.layui-anim').children('dd[lay-value="'+data.mun+'"]').click();
			}
			if(data.provinceid){
				$('select[name="provinceid"]').next().find('.layui-anim').children('dd[lay-value="'+data.provinceid+'"]').click();
			}
			if(data.cityid){
				$('select[name="cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.cityid+'"]').click();
			}
			if(data.three_cityid){
				$('select[name="three_cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.three_cityid+'"]').click();
			}
			
			$("#address").val(data.address);
			$("#linkman").val(data.linkman);
			$("#linktel").val(data.linktel);

			$("#phoneone").val(data.phoneone);
			$("#phonetwo").val(data.phonetwo);
			$("#phonethree").val(data.phonethree);

			$("#linkmail").val(data.linkmail);

			editor.setContent(data.content);

			$("#sdate").val(data.sdate);

			if(data.moneytype){
				$('select[name="moneytype"]').next().find('.layui-anim').children('dd[lay-value="'+data.moneytype+'"]').click();
			}
			$("#money").val(data.money);
			$("#linkjob").val(data.linkjob);
			$("#linkqq").val(data.linkqq);
			$("#website").val(data.website);
			$("#busstops").text(data.busstops);
			if(data.infostatus){
				$('select[name="infostatus"]').next().find('.layui-anim').children('dd[lay-value="'+data.infostatus+'"]').click();
			}
			$.each(data.welfare, function(j,v){
				var welfarename=[];
				$.each(data.welfareall, function(i,val){
					if(val.name==v){
						$("#welfare"+val.name).attr("checked","checked"); 
					}
					welfarename.push(val.name);
				});
				if(!isInArray(welfarename,v)){
					$('#addwelfarelist').append('<input name="welfare[]" value="'+v+'" checked="checked"  type="checkbox" title="'+v+'" data-tag="'+v+'" class="changewelfare" lay-skin="primary">');
				}
			});
			layui.use('form', function() {
				var form = layui.form;  
				form.render();
			});
		});
		parent.layer.closeAll();
	});
}
function isInArray(arr,value){
    for(var i = 0; i < arr.length; i++){
        if(value === arr[i]){
            return true;
        }
    }
    return false;
}
//读取发布职位临时数据
function savejob(){
	var savetype=4;
	parent.layer.confirm("此操作将会覆盖所填写的内容？",function(){
		$.post(weburl+"/member/index.php?m=ajax&c=readform",{savetype:savetype},function(data){ 
			var data=eval('('+data+')');
			$("#name").val(data.name);

			if(data.job_classid){
				$("#job_post").val(data.job_classid);
				$("#workadds_job").val(data.job_class);
			}
			if(data.provinceid){
				$('select[name="provinceid"]').next().find('.layui-anim').children('dd[lay-value="'+data.provinceid+'"]').click();
			}
			if(data.cityid){
				$('select[name="cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.cityid+'"]').click();
			}
			if(data.three_cityid){
				$('select[name="three_cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.three_cityid+'"]').click();
			}
			if(data.salary_type=='1'){
				$("input[name='salary_type']").attr("checked",true);
				$("#minsalary").attr("disabled","disabled");
				$("#maxsalary").attr("disabled","disabled");
 				$("#minsalary").val('0');
				$("#maxsalary").val('0');
			}else{
				$("input[name='salary_type']").attr("checked",false);
				$("#minsalary").val(data.minsalary);
				$("#maxsalary").val(data.maxsalary);
			}

			editor.setContent(data.description);

			if(data.hy){
				$('select[name="hy"]').next().find('.layui-anim').children('dd[lay-value="'+data.hy+'"]').click();
			}
			if(data.number){
				$('select[name="number"]').next().find('.layui-anim').children('dd[lay-value="'+data.number+'"]').click();
			}
			
			if(data.exp){
				$('select[name="exp"]').next().find('.layui-anim').children('dd[lay-value="'+data.exp+'"]').click();
			}

			if(data.report){
				$('select[name="report"]').next().find('.layui-anim').children('dd[lay-value="'+data.report+'"]').click();
			}
			if(data.age){
				$('select[name="age"]').next().find('.layui-anim').children('dd[lay-value="'+data.age+'"]').click();
			}
			if(data.sex){
				$('select[name="sex"]').next().find('.layui-anim').children('dd[lay-value="'+data.sex+'"]').click();
			}
			if(data.edu){
				$('select[name="edu"]').next().find('.layui-anim').children('dd[lay-value="'+data.edu+'"]').click();
			}
			if(data.is_graduate=='1'){
				$("input[name='is_graduate']").attr("checked","checked");
			}	
			if(data.marriage){
				$('select[name="marriage"]').next().find('.layui-anim').children('dd[lay-value="'+data.marriage+'"]').click();
			}

			data.lang.forEach(function(item,index){
				$("#lang"+item).attr("checked","checked"); 
 			});  
 			
			if(data.is_link=='1'){
				$("#islink1").addClass("admin_job_style_n");
				$("#islink2").removeClass('admin_job_style_n');
				$("#islink3").removeClass('admin_job_style_n');
				$("input[name='islink']").val(1);
				$("#newlink").hide();
				$("#tblink").val(2)
			}else if(data.is_link=='2'){
				$("#islink2").addClass("admin_job_style_n");
				$("#islink1").removeClass('admin_job_style_n');
				$("#islink3").removeClass('admin_job_style_n');
				$("input[name='islink']").val(2);
				$("#link_man").val(data.link_man);
				$("#link_moblie").val(data.link_moblie);
				$("#newlink").show();
				$("#tblink").val(data.tblink);
				if(data.tblink==1){
					$("input[name='type_switch']")[0].checked=true;
				}else{
					$("input[name='type_switch']").checked=false;
				}
			}else if(data.is_link=='3'){
				$("#islink3").addClass("admin_job_style_n");
				$("#islink2").removeClass('admin_job_style_n');
				$("#islink1").removeClass('admin_job_style_n');
				$("input[name='islink']").val(3);
				$("#newlink").hide();
			}

			if(data.is_email=='1'){
				$("#isemail1").addClass("admin_job_style_n");
				$("#isemail2").removeClass('admin_job_style_n');
				$("#isemail3").removeClass('admin_job_style_n');
				$("input[name='isemail']").val(1);
				$("#newemail").hide();
			}else if(data.is_email=='2'){
				$("#isemail2").addClass("admin_job_style_n");
				$("#isemail1").removeClass('admin_job_style_n');
				$("#isemail3").removeClass('admin_job_style_n');
				$("input[name='isemail']").val(2);
				$("#email").val(data.email);
 				$("#newemail").show();
			}else if(data.is_email=='3'){
				$("#isemail3").addClass("admin_job_style_n");
				$("#isemail2").removeClass('admin_job_style_n');
				$("#isemail1").removeClass('admin_job_style_n');
				$("input[name='isemail']").val(3);
				$("#newemail").hide();
			}

			layui.use('form', function() {
				var form = layui.form;  
				form.render();
			});

 		});
		parent.layer.closeAll();
	});
}

//读取发布兼职临时数据
function savepart(){
	var savetype=5;
	layer.confirm("此操作将会覆盖所填写的内容？",function(){
		$.post(weburl+"/member/index.php?m=ajax&c=readform",{savetype:savetype},function(data){ 
			var data=eval('('+data+')');
			$("#name").val(data.name);
			if(data.type){
				$('select[name="type"]').next().find('.layui-anim').children('dd[lay-value="'+data.type+'"]').click();
			}
			$("#number").val(data.number);
			data.worktime.forEach(function(item,index){
				$("#worktime"+item).attr("checked","checked"); 
 			});
			if(data.ckAll){
				$("input[name='ckAll']").attr("checked","checked");
			}
			$("#sdate").val(data.sdate);
			if(data.timetype!='1'){
				$("input[name='timetype']").attr("checked",false);
				$("#edate").val(data.edate);
				$("#edate").show();
				$("#deadline").val(data.deadline);
				$("#dline").show();
			}else{
				$("input[name='timetype']").attr("checked",true);
				$("#edate").val("");
				$("#edate").hide();
				$("#deadline").val("");
				$("#dline").hide();
			}
			$("#salary").val(data.salary);
			if(data.salary_type){
				$('select[name="salary_type"]').next().find('.layui-anim').children('dd[lay-value="'+data.salary_type+'"]').click();
			}
			if(data.billing_cycle){
				$('select[name="billing_cycle"]').next().find('.layui-anim').children('dd[lay-value="'+data.billing_cycle+'"]').click();
			}
			editor.setContent(data.content);
			if(data.sex){
				$('select[name="sex"]').next().find('.layui-anim').children('dd[lay-value="'+data.sex+'"]').click();
			}
			if(data.provinceid){
				$('select[name="provinceid"]').next().find('.layui-anim').children('dd[lay-value="'+data.provinceid+'"]').click();
			}
			if(data.cityid){
				$('select[name="cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.cityid+'"]').click();
			}
			if(data.three_cityid){
				$('select[name="three_cityid"]').next().find('.layui-anim').children('dd[lay-value="'+data.three_cityid+'"]').click();
			}
			$("#address").val(data.address);
			$("#map_x").val(data.x);
			$("#map_y").val(data.y);
			$("#linkman").val(data.linkman);
			$("#linktel").val(data.linktel);
			setLocation('map_container',data.x,data.y,"map_x","map_y");
			layui.use('form', function() {
				var form = layui.form;  
				form.render();
			});
		});
		parent.layer.closeAll();
	});
}

$(document).ready(function() {
	$("#close").click(function(){
		$("#forms").slideToggle("normal");
	}); 
});