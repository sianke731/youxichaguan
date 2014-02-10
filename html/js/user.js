// 用户中心的一些js

var userMode = {
	userSideHeight:function(){
		var h = $('.user-centent').height();
		if(h > 700){
			$('.user-side').css('height',h+'px');
		}
	},
	//显示修改头像的按钮
	showEditAvatarBtn:function(){
		$('#js_avatar_box').hover(function(){
			$(this).find('.edit-avatar').show();
		},function(){
			$(this).find('.edit-avatar').hide();
		});
	},
	//标签删除
	tagRemove:function(){
		$('.sel-lis').find('em').die().live('click',function(){
			$(this).parent().remove();
		});
	},
	init:function(){
		this.showEditAvatarBtn();
		this.tagRemove();
		this.userSideHeight();
	}
};

$(document).ready(function(){
	userMode.init();
})