//下载简历 
$(document).ready(function() {
	$("input[name='background_type']").click(function() {
		if($(this).val() == '1') {
			$(".resume_background_color").hide();
			$(".resume_background_image").show();
		} else if($(this).val() == '2') {
			$(".resume_background_image").hide();
			$(".resume_background_color").show();
		} else {
			$(".resume_background_color").show();
			$(".resume_background_image").show();
		}
	});
})

//下载简历金额付费
function buyResume(eid, type) {
	if(type == '2') {
		$.layer({
			type: 1,
			title: "下载简历",
			closeBtn: [0, true],
			border: [10, 0.3, '#000', true],
			area: ['480px', '300px'],
			page: {
				dom: "#downltresume_pay"
			}
		});
	} else {
		$.layer({
			type: 1,
			title: "下载简历",
			closeBtn: [0, true],
			border: [10, 0.3, '#000', true],
			area: ['480px', '300px'],
			page: {
				dom: "#downresume_pay"
			}
		});
	}

}

//下载简历金额付费
function buyLtResume(eid) {
	$.layer({
		type: 1,
		title: "下载简历",
		closeBtn: [0, true],
		border: [10, 0.3, '#000', true],
		area: ['480px', '300px'],
		page: {
			dom: "#ltdownresume"
		}
	});
}

// 查看联系方式，提示剩余下载简历数
function isDownResume(eid, num, url) {
	if(isHeight == '2') {
		var i = layer.confirm('您还可以查看' + num + '份高级简历，是否查看？', {
				btn: ['查看', '取消']
			},
			function() {
				layer.close(i);
				for_link(eid, url);
			},
			function() {
				layer.close(i);
			}
		);
	} else {
		var i = layer.confirm('您还可以查看' + num + '份简历，是否查看？', {
				btn: ['查看', '取消']
			},
			function() {
				layer.close(i);
				for_link(eid, url);
			},
			function() {
				layer.close(i);
			}
		);
	}

}

//简历详情页查看联系方式、下载简历
function for_link(eid, url, todown) {
	var i = layer.load('执行中，请稍候...', 0);
	$.post(url, {
		eid: eid
	}, function(data) {
		layer.close(i);
		var data = eval('(' + data + ')');
		var status = data.status;
		var type = data.type;
		if(status == 1) {
			var j = layer.confirm('发布职位后才可以下载简历', {
					btn: ['前去发布', '继续浏览']
				},
				function() {
					window.location.href = weburl + "/member/index.php?c=jobadd";
					window.event.returnValue = false;
					return false;
				},
				function() {
					layer.close(j);
					window.location.href = window.location.href;
				}
			);
		} else if(status == 2) {

			if(data.usertype == '2') {

				var msglayer = layer.open({
					type: 1,
					title: '提示信息',
					closeBtn: 1,
					border: [10, 0.3, '#000', true],
					area: ['550px', 'auto'],
					content: $("#tcmsg")
				});
				$("#msg_show").html(data.msg);
				$("#btn_value").html('<a href="javascript:void(0);" onclick="buyResume(eid,type);">确定</a>');

				/*var k = layer.confirm(data.msg, function() {
					layer.close(k);
					buyResume(eid, type);
				});*/

			} else if(data.usertype == '3') {
				layer.confirm(data.msg, function() {
					buyLtResume(eid);
				});
			}
		} else if(status == 3) {
			if(todown) {
				$("#for_link .btn").hide();
				$("#chat_connection").val('2'); //下载简历后设置聊天状态
				window.location.href = todown;
			}
			$("#for_link .city_1").html(data.html);
			$.layer({
				type: 1,
				title: "查看联系方式",
				offset: [($(window).height() - 150) / 2 + 'px', ''],
				closeBtn: [0, true],
				border: [10, 0.3, '#000', true],
				area: ['390px', 'auto'],
				page: {
					dom: "#for_link"
				},
				close: function(index) {
					window.location.href = window.location.href;
				}
			});
		} else if(status == 4) {
			var m = layer.confirm(data.msg, function() {
				layer.close(m);
				showpacklist();
			});
		} else if(status == 5) {
			if(todown) {
				$("#for_link .btn").hide();
				$("#chat_connection").val('2'); //下载简历后设置聊天状态
				window.location.href = todown;
			}
		} else if(status == 6) {
			var n = layer.confirm('您的帐号未通过审核，请联系客服加快审核进度！', function() {
				layer.close(n);
			});
		} else if(status == 7) {
			var m = layer.confirm(data.msg, function() {
				layer.close(m);
				window.location.href = weburl + "/member/index.php?c=right";
				window.event.returnValue = false;
				return false;
			});
		} else {
			layer.msg(data.msg, 2, 8);
		}
	});
}

function showpacklist() {
	var i = layer.load('执行中，请稍候...', 0);
	$.post(weburl + "/index.php?m=ajax&c=getPack", {
		rand: Math.random()
	}, function(data) {
		layer.close(i);
		$("#ratinglist").html(data);
		$.layer({
			type: 1,
			title: "购买增值服务",
			offset: ['10px', ''],
			closeBtn: [0, true],
			border: [10, 0.3, '#000', true],
			area: ['850px', '567px'],
			page: {
				dom: "#packlist"
			}
		});
	})

}

function checkpack() {
	var comvip = $("input[name=comvip]:checked").val();
	if(comvip > 0) {
		return true;
	} else {
		layer.msg("请先选择增值包！", 2, 8);
		return false;
	}
}

function check_comvip(price) {
	$(".Download_resume_tips_f").html("￥" + price);
	$(".Download_resume_tips_jine").show();
}

//下载简历积分模式
function down_integral(eid, uid, url) {
	$.post(url, {
		type: "integral",
		eid: eid,
		uid: uid
	}, function(data) {
		var data = eval('(' + data + ')');
		var status = data.status;
		var integral = data.integral;
		if(status == 5) {
			layer.confirm('您还有' + integral + integral_pricename + '！不够下载简历，是否充值？', function() {
				window.location.href = weburl + "/member/index.php?c=pay";
				window.event.returnValue = false;
				return false;
			});
		} else if(status == 3 || status == 6) {
			$("#for_link .city_1").html(data.html);
			$.layer({
				type: 1,
				title: "查看联系方式",
				offset: [($(window).height() - 150) / 2 + 'px', ''],
				closeBtn: [0, true],
				border: [10, 0.3, '#000', true],
				area: ['350px', 'auto'],
				page: {
					dom: "#for_link"
				}
			});
		} else {
			layer.msg(data.msg, 2, 8);
			return false;
		}
	})
}
//猎头下载简历积分模式
function lt_down_integral(eid, uid, url) {
	$.post(url, {
		type: "integral",
		eid: eid,
		uid: uid
	}, function(data) {
		var data = eval('(' + data + ')');
		var status = data.status;
		var integral = data.integral;
		if(status == 5) {
			layer.confirm('您还有' + integral + integral_pricename + '！不够下载简历，是否充值？', function() {
				window.location.href = weburl + "/member/index.php?c=pay";
				window.event.returnValue = false;
				return false;
			});
		} else if(status == 3) {
			$("#for_link .city_1").html(data.html);
			$.layer({
				type: 1,
				title: "查看联系方式",
				offset: [($(window).height() - 150) / 2 + 'px', ''],
				closeBtn: [0, true],
				border: [10, 0.3, '#000', true],
				area: ['350px', 'auto'],
				page: {
					dom: "#for_link"
				}
			});
		}
	})
}

function settemplate(msg, url) {
	layer.confirm(msg, function() {
		var i = layer.load('执行中，请稍候...', 0);
		$.ajaxSetup({
			cache: false
		});
		$.get(url, function(data) {
			layer.close(i);
			var data = eval('(' + data + ')');
			if(data.st == '8') {
				layer.msg(data.msg, Number(data.tm), Number(data.st), function() {
					layer.closeAll('iframe');
				});
				return false;
			} else {
				layer.msg(data.msg, Number(data.tm), Number(data.st), function() {
					location.href = data.url;
				});
				return false;
			}
		});
	});
}

function add_user_talent(uid, usertype) {
	if(usertype == "2") {
		$.layer({
			type: 1,
			title: "添加备注",
			closeBtn: [0, true],
			border: [10, 0.3, '#000', true],
			area: ['380px', '200px'],
			page: {
				dom: "#talent_pool_beizhu"
			}
		});
	} else if(usertype == "") {
		showlogin('2');
	} else {
		layer.msg('只有企业用户，才可以操作！', 2, 8);
		return false;
	}
}

function talent_pool(uid, eid, url) {
	var remark = $("#remark").val();
	$.post(url, {
		eid: eid,
		uid: uid,
		remark: remark
	}, function(data) {
		if(data == '0') {
			layer.msg('只有企业用户，才可以操作！', 2, 8);
		} else if(data == '1') {
			layer.msg('加入成功！', 2, 9, function() {
				location.reload();
			});
		} else if(data == '2') {
			layer.msg('该简历已加入到人才库！', 2, 8, function() {
				layer.close(layer.index);
			});
		} else {
			layer.msg('对不起，操作出错！', 2, 8);
		}
	});
}

function addjob() {
	var i = layer.confirm('发布职位后才可以下载简历', {
			btn: ['前去发布', '继续浏览']
		},
		function() {
			window.location.href = weburl + "/member/index.php?c=jobadd";
			window.event.returnValue = false;
			return false;
		},
		function() {
			layer.close(i);
			window.location.href = window.location.href;
		}
	);
}

function dayin() {
	$("#operation_box").hide();
	window.print();
}

//检查上一次推荐职位、简历的时间间隔
function recommendInterval(uid, url) {
	var ajaxUrl = weburl + "/index.php?m=ajax&c=ajax_recommend_interval";
	$.post(ajaxUrl, {
		uid: uid
	}, function(data) {
		data = eval('(' + data + ')');
		if(data.status == 0) {
			window.location = url;
		} else {
			layer.msg(data.msg, 3, 8);
		}
	});
}