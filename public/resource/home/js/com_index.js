//职位搜索页 JS
$(document).ready(function(){
//职位搜索页 收藏职位 type:普通职位 1 ，公司发布的猎头职位 2，猎头发布的职位 3	
	$(".checkbox_job").click(function(data){//全选
		var val=$(this).attr("class");
		if(val=="checkbox_job"){
			$(this).addClass("iselect")
			var pid=$(this).attr("pid");
			$("#checkbox"+pid).attr("checked","checked");
		}else{
			$(this).removeClass("iselect")
			var pid=$(this).attr("pid");
			$("#checkbox"+pid).attr("checked",false);
			$(".checkbox_all").removeClass("iselect")
		}
	})
	$('body').mouseout(function(evt){
		if($(evt.target).parents('.com-list-wrapper').length==0){
		   $('.ks-popup').hide();
		}
	})
})
function checkAll(){//全选
	var val=$(".checkbox_all").attr("class");
	if(val=="checkbox_all"){
		$("input[name=checkbox_job]").attr("checked","checked");
		$(".checkbox_job").addClass("iselect")
		$(".checkbox_all").addClass("iselect")
	}else{
		$("input[name=checkbox_job]").attr("checked",false);
		$(".checkbox_job").removeClass("iselect")
		$(".checkbox_all").removeClass("iselect")
	}
}
function exchange(){
	var exchangep=$("#exchangep").val();
	$.get(weburl+"/index.php?m=ajax&c=exchange&page="+exchangep,function(data){
		
		$(".job_recommendation_list").html(data);
	});
}
$(document).ready(function(){
	$(".yun_Looking_work_name").hover(function(){
		var aid=$(this).attr("aid");
		$("#i"+aid).addClass("All_post_seach_lbg");
	},function(){
		var aid=$(this).attr("aid");
		$("#i"+aid).removeClass("All_post_seach_lbg");
	}); 
}); 