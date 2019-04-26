function checkmore(type){
	var html=$("#"+type).html();
	//$("."+type).slideToggle();
	$("."+type).toggle();
	if(html=="更多"){
		$("#"+type).attr('class','showcheck');
		$("#"+type).html('收起');
	}else{
		$("#"+type).attr('class','hidecheck');
		$("#"+type).html('更多');
	}
}
$(document).ready(function(){
	$('.Search_jobs_more_chlose').hover(function(){
		$(this).find('.none').show();  
	},function(){
		$(this).find('.none').hide(); 
	});
	
	$('.delete').live('click',function(){
		var id = $(this).attr('data-id');
		var pid = $(this).attr('data-pid');
		if(parseInt(pid)>0){
			unsel(id,pid);
		}else{
			unsel(id)	
		}
	});	
	$('.search_job_list').hover(function(){
		$(".search_job_list").removeClass("search_job_list_cur_line");
		$(this).addClass('search_job_list_cur_line');  
		$(".search_job_list_cur_line>.search_job_list_box").show();
	},function(){
		var ltype=$('#ltype').val();
		if(ltype==''){
			$(".search_job_list_cur_line>.search_job_list_box").hide();
			$(".search_job_list").removeClass("search_job_list_cur_line");}
		} 
	);
	
	//搜索城市列表一二级展现
	$('.Search_jobs_sub_a').bind('mouseenter',function(){
		var dataid = $(this).attr('data-id');
		if(dataid){
			$('.Search_jobs_select').hide();
			$('#citytype'+dataid).show();
			//根据当前位置调整指向箭头
			var leftPx = $(this).position().left; 
			//调整箭头位置
			$('#icon_'+dataid).css('left',leftPx-60);
		}
		
	});
	$('.Search_jobs_form_list').bind('mouseleave',function(){
		$('.Search_jobs_select').hide();
		$('.oldshow').show();
	});
	/*找工作、找人才新加更多4.3(hl)*/ 
	$('#acity').hover(function(){
		$('.Search_cityall').removeClass('none');  
	},function(){
		$('.Search_cityall').addClass('none');
	});
	$('.Search_cityall').hover(function(){
		$('.Search_cityall').removeClass('none');  
	},function(){
		$('.Search_cityall').addClass('none'); 
	});
	/*(hl)结束*/ 
});

function addfinder(para,usertype,type){
	if(para==''){
		layer.msg('没有条件，无法保存！',2,8);return false;
	}
	$.post(weburl+"/job/index.php?c=addfinder",{para:para,usertype:usertype},function(data){
		var data=eval('('+data+')');
		if(type=='1'){
			layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;		
		}else{
			layer.msg(data.msg, Number(data.tm), Number(data.st));return false;		
		} 
	});
}
function showurl(url){
	window.location.href=url;
}
//找人才、找工作城市新加 4.3
function acityshow(id){
	if(id==1){
		$(".acity_two").addClass('search_city_active');
		$(".acity_three").removeClass('search_city_active');
		$("#acity_two").removeClass('none');
		$("#acity_three").addClass('none');
	}else if(id==2){
		$(".acity_three").addClass('search_city_active');
		$(".acity_two").removeClass('search_city_active');
		$("#acity_two").addClass('none');
		$("#acity_three").removeClass('none');
	}
}