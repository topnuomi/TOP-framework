//JS -- 前台首页
$(function(){	
	//公告滚动
	marquee("2000",".wantedjob_cont  ul"); 
	//微信招聘
    $(".hp_z_w").hover(function() {
		$('.hp_z_w_icon').show();
		$('.hp_z_s_icon').hide();
        $('#hp_weixin').show();
		$('#hp_phone').hide();
    });
	//手机招聘
	$(".hp_z_s").hover(function() {
		$('.hp_z_w_icon').hide();
		$('.hp_z_s_icon').show();
        $('#hp_phone').show();
        $('#hp_weixin').hide();
    });
	//下拉框点击页面其他位置隐藏
	$('body').click(function(evt) {
		if($(evt.target).parents(".index_r_wap_l").length==0&&$(evt.target).attr('class')!='index_r_wap_l') {
			$(".index_r_wap_l>.index_r_wap_box").hide();
		} 
		if($(evt.target).parents(".index_r_weixin").length==0&&$(evt.target).attr('class')!='index_r_weixin') {
			$(".index_r_wap_box_weixin").hide();
		} 
		if($(evt.target).parents(".index_r_wap_box").length){
			$(".index_r_wap_box").hide();
		}
		if($(evt.target).parents(".index_nav_tit").length==0){
			$("#boxNav").hide();
		}
	});  
	/*首页右侧新闻、公告切换*/
	$(".yun_index_h1_list li").hover(function(){
		var num=$(this).index(); 
		$(".yun_index_h1_list li").removeClass("yun_index_h1_cur");
		$(this).addClass("yun_index_h1_cur");
		$(".yuin_index_r .yun_index_cont").hide();
		$(".yun_index_cont:eq("+num+")").show(); 
	}); 
	$(".index_r_wap_l").click(function(){
		$(".index_r_wap_box").hide();
		$(this).find(".index_r_wap_box").show();
	});
	$(".index_r_weixin").click(function(){
		$(".index_r_wap_box").hide();
		$(this).find(".index_r_wap_box").show();
	});

	/*首页类别开始*/
	$("#navMenu").click(function(){
		$("#boxNav").show();
	});
	
	$("#navLst li").hover(function(){
		$(this).attr('class','show');
	},function(){
		$(this).attr('class','');
	});

	$(".moreOptions").hover(function(){
		$(".moredetails").show();
	},function(){
		$(".moredetails").hide();
	});
	/*
	$("#navLst .index_nav_l").hover(function(){
		$(this).parent().attr('class','show');
	},function(){$(this).parent().attr('class','');});*/
	/*首页类别结束*/

	/*首页置顶简历悬浮点切换*/
	 
	$("#BoxSwitch span").hover(function(){
		 
 		$("#BoxSwitch span").removeClass("cur");
		$(this).addClass("cur");
		var id=$(this).attr('data-id');

		$('.index_resume_user_zd_list').hide();
		$('.rtop'+id).show();
  	}); 
	
	$("#btnl").click(function(){
		if($("#cur1").length>0 && $("#cur2").hasClass('cur')){
			$("#cur2").removeClass('cur');
			$("#cur1").addClass('cur');
			$(".rtop1").show();
			$(".rtop2").hide();
			$(".rtop3").hide();
			$(".rtop4").hide(); 
		}else if($("#cur2").length>0 && $("#cur3").hasClass('cur')){
			$("#cur3").removeClass('cur');
			$("#cur2").addClass('cur');
			$(".rtop2").show();
			$(".rtop1").hide();
			$(".rtop3").hide();
			$(".rtop4").hide(); 
		}else if($("#cur3").length>0 && $("#cur4").hasClass('cur')){
			$("#cur4").removeClass('cur');
			$("#cur3").addClass('cur');
			$(".rtop3").show();
			$(".rtop2").hide();
			$(".rtop1").hide();
			$(".rtop4").hide(); 
		}
	})

	$("#btnr").click(function(){

		if($("#cur2").length>0 && $("#cur1").hasClass('cur')){
			$("#cur1").removeClass('cur');
			$("#cur2").addClass('cur');
			$(".rtop2").show();
			$(".rtop1").hide();
			$(".rtop3").hide();
			$(".rtop4").hide(); 
		}else if($("#cur3").length>0 && $("#cur2").hasClass('cur')){
			$("#cur2").removeClass('cur');
			$("#cur3").addClass('cur');
			$(".rtop3").show();
			$(".rtop2").hide();
			$(".rtop1").hide();
			$(".rtop4").hide();
		}else if($("#cur4").length>0 && $("#cur3").hasClass('cur')){
			$("#cur3").removeClass('cur');
			$("#cur4").addClass('cur');
			$(".rtop4").show();
			$(".rtop2").hide();
			$(".rtop3").hide();
			$(".rtop1").hide();
		}
	})
	 
})

/*首页广告*/
$(document).ready(function(){	
	$('#bottom_ad_is_show').val('1');
	var duilian = $("div.duilian");
	var duilian_close = $(".btn_close");
	var scroll_Top = $(window).scrollTop();
	var window_w = $(window).width();
	if(window_w>1000){duilian.show();}
	buttom_ad();
	$("div .duilian").css("top",scroll_Top+200);
	$(window).scroll(function(){
		buttom_ad();
		var scroll_Top = $(window).scrollTop();
		duilian.stop().animate({top:scroll_Top+200});
	});
	duilian_close.click(function(){
		$(this).parents('.duilian').hide();
		return false;
	});
});

function colse_bottom(){
	$("#bottom_ad_fl").parent().hide();
	$('#bottom_ad_is_show').val('0');
}
function buttom_ad(){
	if($("#bottom_ad").length>0&&$("#bottom_ad_is_show").length>0){
		var scrollTop = $(window).scrollTop();
		var w_height=$(document).height();
		var bottom_ad=$("#bottom_ad").offset().top;
		var bottom_ad_fl=$("#bottom_ad_fl").offset().top;
		var poor_height=parseInt(w_height)-parseInt(scrollTop);
		var bottom_ad_is_show=$('#bottom_ad_is_show').val();
		if(window.attachEvent){
			poor_height=parseInt(poor_height)-parseInt(22);
		}
		if(poor_height<=880){
			$("#bottom_ad_fl").parent().hide();
		}else if(bottom_ad_is_show=='1'){
			$("#bottom_ad_fl").parent().show();
		}
	}
}
/*首页广告结束*/
function showDiv2(obj){
	if($(obj).attr("class")=="current1"){
		$(obj).removeClass();
	}
	else{
		$(obj).addClass("current1");
		$(obj).find(".shade").height($(obj).find(".area").height()+60)
	}
}
/*首页获取职位类别
function show_job(id,showhtml){
	if(showhtml=="1"){
		$.post("index.php?m=ajax&c=show_leftjob",{},function(data){	
			$("#menuLst").html(data);	
			$(".lst"+id).attr("class","lst"+id+" hov");			
		});
	}else{
		var num=$(".lstCon").length/3;
		if(id<num){
			var height=id*35;
			var heightdiv=$(".lst"+id+" .lstCon").height();
			if(heightdiv-height<35){
				height=heightdiv=$(".lst"+id+" .lstCon").height()/2;
			}
			$(".lst"+id+" .lstCon").attr("style","top:-"+height+"px");
		}else if(id<num*2){
			var height=id*35;
			var heightdiv=$(".lst"+id+" .lstCon").height()/2;
			$(".lst"+id+" .lstCon").attr("style","top:-"+heightdiv+"px");
		}else{
			var height=($(".lstCon").length-id)*35;
			var heightdiv=$(".lst"+id+" .lstCon").height();
			if(heightdiv>height){
				heightdiv=heightdiv-height;
			}else{
				heightdiv=0;
			}
			$(".lst"+id+" .lstCon").attr("style","top:-"+heightdiv+"px");
		}
		$(".lst"+id).attr("class","lst"+id+" hov");	
	}
}
function hide_job(id){
	$("#menuLst li").removeClass("hov"); 
}
*/