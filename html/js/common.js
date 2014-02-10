/*
 *基础js
 *lihua
*/
//浏览器版本和厂商检查
(function () {
	window.sys = {};
	var ua = navigator.userAgent.toLowerCase();	
	var s;		
	(s = ua.match(/msie ([\d.]+)/)) ? sys.ie = s[1] :
	(s = ua.match(/firefox\/([\d.]+)/)) ? sys.firefox = s[1] :
	(s = ua.match(/chrome\/([\d.]+)/)) ? sys.chrome = s[1] : 
	(s = ua.match(/opera\/.*version\/([\d.]+)/)) ? sys.opera = s[1] : 
	(s = ua.match(/version\/([\d.]+).*safari/)) ? sys.safari = s[1] : 0;
	if (/webkit/.test(ua)) sys.webkit = ua.match(/webkit\/([\d.]+)/)[1];
})();

//焦点图插件
jQuery.slider = function(options){
	var defaults = {
		imgbox:$('#js_i_slider_pic'),
		btnbox:$('#js_i_slider_btn'),
		curr:'is-curr',
		ev:'click',
		eff:'alpha',
		timer:2000,
		speed:300
	};
	var opt = $.extend(defaults,options);
	var imgli = opt.imgbox.children();
	var btnli = opt.btnbox.children();
	var isplay = null;
	var index = 0;
	var len = imgli.length;
	var onew = imgli.eq(0).width();
	var oneh = imgli.eq(0).height();
	var iNow = 0;
	var flag = true;
	//初始化
	switch(opt.eff){
		case 'left':
			imgli.css({
				position:'absolute',
				left: onew+'px'
			});
			imgli.eq(0).css('left','0px');				
			break;
		case 'up':
			imgli.css({
				float:'none',
				position:'absolute',
				top:oneh+'px'
			});
			imgli.eq(0).css('top','0px');
			break;
		case 'alpha':
			imgli.css({
				position:'absolute',
				left:0,
				top:0,
				opacity:0
			});
			imgli.eq(0).css({
				opacity:1
			});
			break;
		default:
			imgli.css({
				position:'absolute',
				left:0,
				top:0,
				display:'none'
			});
			imgli.eq(0).css('display','block');
	}
	btnli.eq(0).addClass(opt.curr);
	// 播放函数
	var _play = function(num){
		var index = num;
		if(flag){
			flag = false;
			switch(opt.eff){
				case 'left'://左右切换
					if(iNow < index){
						imgli.eq(index).css('left' , onew+'px' );
						imgli.eq(iNow).animate({
								left: -onew+'px'
						},opt.speed);
					}else if(iNow > index){
						imgli.eq(index).css('left' , -onew+'px' );
						imgli.eq(iNow).animate({
							left : onew+'px'
						},opt.speed);
					}
					imgli.eq(index).animate({
						left : 0
					},opt.speed,function(){
						flag = true;
					});
					iNow = index;
					break;
					case 'up' : //上下切换
						if(iNow < index){
							imgli.eq(index).css('top',oneh+'px');
							imgli.eq(iNow).animate({
								top:-oneh+'px'
							},opt.speed);
						}else if(iNow > index){
							imgli.eq(index).css('top',-oneh + 'px');
							imgli.eq(iNow).animate({top:oneh+'px'},opt.speed);
						}
						imgli.eq(index).animate({top:0},opt.speed,function(){
							flag = true;
						});
						iNow = index;
						break;
					case 'alpha': //渐变
						imgli.eq(num).animate({
							opacity:1
						},opt.speed);
						imgli.eq(num).siblings().animate({
							opacity:0
						},opt.speed,function(){
							flag = true;
						});
						break;
					default: //没有切换效果
						imgli.css({
							position:'absolute',
							left:0,
							top:0,
							display:'none'
							});
						imgli.eq(num).css('display','block');
						flag = true;
				}
				btnli.eq(num).addClass(opt.curr).siblings().removeClass(opt.curr);	
		}
	}
	//自动播放
	var _autoplay = function(){
		clearInterval(isplay);
		isplay = setInterval(function(){
			index++;
			index = index >= len ? 0 : index++;
			_play(index);
		},opt.timer);
		
	};
	//初始化自动播放
	_autoplay();
	//事件绑定
	btnli.bind(opt.ev,function(){
		clearInterval(isplay);
		index = $(this).index();
		if($(this).attr('class') != opt.curr){
			_play(index);
		}
	}).mouseout(function(){
		_autoplay();
	});
	imgli.mouseover(function(){
		clearInterval(isplay);
	}).mouseout(function(){
		_autoplay();
	});
}

//图片滚动插件
jQuery.scrollpic = function(options){
		var defaults = {
		box : $('#js_scroll_ul'),
		slen : 1,
		speed:300,
		prev:$('#js_prev'),
		next:$('#js_next'),
		dire:'left'
	};
	var opt = $.extend(defaults,options);
	var ul = opt.box;
	var li = ul.children();
	var num = opt.slen;
	var len = li.length;
	var w = li.eq(0).width();
	var h = li.eq(0).height();
	var isAnimated = true;
	var autoWidth =function(){
		ul.css('width',ul.children().length*w+'px');
	}
	switch(opt.dire){
		case 'left':
			autoWidth();
			break;
		case 'up':
			li.css('float','none');
			opt.box.css({
				width:w+'px',
				top:0
			});
			break;
	}
	if(len > num){
		opt.next.click(function(){
			if(isAnimated){
				isAnimated = false;
				switch(opt.dire){
					case 'left':
						var li = ul.children();
						for(var i=0; i < num; i++){
							ul.append(li.eq(i).clone(true,true));
							autoWidth();
						}
						ul.animate({
							left: -w*num+'px'		  
						},opt.speed,function(){
							ul.find('li:gt('+(len-1)+')').remove();
							for(var i=0; i < num; i++){
								ul.append(li.eq(i));
								ul.css('left',0);
							}
							isAnimated = true;
						});
						break;
					case 'up':
						var li = ul.children();
						for(var i=0; i < num; i++){
							ul.append(li.eq(i).clone(true,true));
						}
						ul.animate({
							top: -h*num+'px'		  
						},opt.speed,function(){
							ul.find('li:gt('+(len-1)+')').remove();
							for(var i=0; i < num; i++){
								ul.append(li.eq(i));
								ul.css('top',0);
							}
							isAnimated = true;
						});
						break;
				}
			}
		});
	}
	if(len > num){
		opt.prev.click(function(){
			if(isAnimated){
				isAnimated = false;
				switch(opt.dire){
					case 'left':
						var li = ul.children();
						for(var i=1; i<=num; i++){
							ul.prepend(li.eq(len-i).clone(true,true));
							autoWidth();
							ul.css('left',-w*num+'px');
						}
						ul.animate({
							left:0
						},opt.speed,function(){
							var li = ul.children();
							for(var i=0; i<=num; i++){
								ul.find('li:gt('+(len-1)+')').remove();
							}
							isAnimated = true;
						});
						break;
					case 'up':
						var li = ul.children();
						for(var i=1; i<=num; i++){
							ul.prepend(li.eq(len-i).clone(true,true));
							ul.css('top',-h*num+'px');
						}
						ul.animate({
							top:0
						},opt.speed,function(){
							var li = ul.children();
							for(var i=0; i<=num; i++){
								ul.find('li:gt('+(len-1)+')').remove();
							}
							isAnimated = true;
						});
						break;
				}
			}
		});
	  }
	}


//倒计时组件
jQuery.countDownTime = function(options){
	var defaults = {
		etime:new Date('2014/01/15 13:00:00'),
		days:$('#day'),
		hours:$('#hour'),
		minutes:$('#minute'),
		seconds:$('#second')
	};
	var opt = $.extend(defaults,options);
	var das = opt.etime;
	var timers = null;
	var countDowns = function(){
		var now = new Date();
		var date = das.getTime() - now.getTime();
		var maxTime = Math.floor(date/1000);	
		maxTime--;
		if(maxTime >= 0){
			var day = Math.floor( (maxTime/60/60/24));
			var hour = Math.floor( (maxTime/60/60)%60);
			var minute = Math.floor( (maxTime/60)%60);
			var second = Math.floor( maxTime%60 );
			if(day < 10){
				day = "0" + day;
			}
			if(hour<10){
				hour = "0" + hour;
			}
			if(minute<10){
				minute = "0" + minute;
			}
			if(second<10){
				second = "0" + second;
			}
			opt.days.html(day);
			opt.hours.html(hour);
			opt.minutes.html(minute);
			opt.seconds.html(second);
		}
	}
	timers = setInterval(function(){
		countDowns();
	},1000);	
}

//公用模块
var M = {
	//去掉多余的样式
	publicStyleOper:function(){
		//导航条去掉多余的线条
		$('#js_menu_box li').eq(0).css('border','0');
		$('#js_menu_box li:last').css('borderRight','0');
		//搜索条下拉取值
		$('#js_s_info').hover(function(){
			$(this).find('.search-list').slideDown(150);
		},function(){
			$(this).find('.search-list').slideUp(150);
		});
		$('.search-list li').click(function(){
			var fm = $('#search-fm');
			var str = $(this).html();
			var action = $(this).attr('data-id');
			$('#js_s_t').find('.fn').html(str);
			fm.attr('action',action);
			$('.search-list').slideUp(150);
		});
		//设置搜索框url地址
		$('#search-fm').on('submit',function(){
			var str = $('#js_s_t').find('.fn').html();
			if($.trim(str) == '请选择'){
				var action = $('.search-list li').eq(0).attr('data-id');
				var fm = $('#search-fm');
				fm.attr('action',action);
			}
			
			
			var key = $.trim($('#js_search_ipt').val());
			var action = $('#search-fm').attr('action');
			window.location.href = action+key;
			return false;
		});
	},
	//返回顶部
	publicReturnTop:function(){
		var t = $('#js_rtop');
		t.hide();
		$(window).scroll(function(){
			var top = $(window).scrollTop();
			if(top > 180){
				t.fadeIn(200);
			}else{
				t.fadeOut(200);
			}
		});
		t.bind('click',function(){
			$('html,body').animate({
				scrollTop:0
			},300);
		});
	},
	//顶部下拉菜单
	publicDownList:function(){
		$('#js_login_yes').hover(function(){
			$(this).addClass('current');
			$(this).find('.user-oper-list').slideDown(200);
		},function(){
			$(this).removeClass('current');
			$(this).find('.user-oper-list').slideUp(100);
		});
	},
	publicYjfk:function(){
		$('#js_ly').die().live('click',function(){
			dialog._create({
				width:520,
				top:170, 
				title:'意见反馈', 
				url:'index.php?app=public&mod=Index&act=suggest',
				callback:function(){
					
				}
			});
		});
	}
};

//拖动
M.darg = function(obj, ele, vW){
	/*
	*传入一个dom对象 $('#box1')[0]...
	*obj： 要拖动的元素
	*ele :拖动的范围  $('#box1 h2')[0]...
	*vw 拖动的左右宽度
	*/
	var obj = obj;
	var vW = vW ? vW:document.documentElement.clientWidth; 
	var disX = 0;        
	var disY = 0;
	ele.onmousedown = function(ev){          
		var oEvent = ev || event;                        
		disX = oEvent.clientX - obj.offsetLeft;                      
		disY = oEvent.clientY - obj.offsetTop;                        
		if (obj.setCapture) {                        
			obj.onmousemove = fnMove;                                    
			obj.onmouseup = fnUp;                                    
			obj.setCapture();                                    
		}       
		else {       
			document.onmousemove = fnMove;
			document.onmouseup = fnUp;           
		}
	   function fnUp(){
		   this.onmousemove = null;
		   this.onmouseup = null;
			if (this.releaseCapture)             
				this.releaseCapture();
	   }
	   function fnMove(ev){
		   var oEvent = ev || event;
		   var l = oEvent.clientX - disX;
		   var t = oEvent.clientY - disY;
		   var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		   var w = vW- obj.offsetWidth;
		   var h = document.documentElement.clientHeight - obj.offsetHeight + scrollTop;
		   if (l < 10)             
				l = 0;
		   if (l > w - 10)             
				l = w;            
			if (t < 10)             
				t = 0;
			if (t > h - 10)            
				t = h;
		   obj.style.left = l + 'px';
		   obj.style.top = t + 'px';
		}
	   return false;
   }
}



//弹框
dialog = {
	/*
	*width	:弹出层的宽度(可选,默认为530)
	*top	:弹出层top值(可选,默认为居中)
	*title 	:弹出层标题展示文字(默认为"提示信息")
	*msg	:弹出层具体展示内容(提示功能)
	*url	:弹出层具体页面(把要放到弹出层里面展示的页面的url传进来,不能跨域)
	*callback: 当页面load进来之后，回调函数( 可以对load进来的页面进行操作)
	*/
	_create:function(obj){
		//内容html
		var dialog_html = '<div id="dialog-bg"></div>';
		if(obj.width){
			dialog_html+= '<div id="dialog-main" style="width:'+obj.width+'px">';
		}else{
			dialog_html+= '<div id="dialog-main">';
		}
		if(obj.title){
			dialog_html+= '<h2 id="dialog-tit" class="dialog-tit"><span id="d-tit">'+obj.title+'</span>';	
		}else{
			dialog_html+= '<h2 id="dialog-tit" class="dialog-tit"><span id="d-tit">提示信息</span>';
		}
		dialog_html+='<a href="javascript:;" id="dialog-close" title="关闭"></a>';
		dialog_html+='</h2>';
		dialog_html+='<div id="dialog-box"><span class="m15 db fs14" style="color:#999;">努力加载中...</span></div>';
		dialog_html+='</div>';
		$('body').append(dialog_html);
		var d_bg = $('#dialog-bg');
		var d_main = $('#dialog-main');
		var d_close = $('#dialog-close');
		var d_box = $('#dialog-box');
		
		if(obj.url){
			$('#dialog-box').load(obj.url,obj.callback);
		}else if(obj.ascMsg){
			var ts = '<div class="tc">';
				ts+='<p class="dia-msg dia-msg-asc">'+obj.ascMsg+'</p>';
				ts+='<a href="javascript:;" id="js_dia_btn" class="ico-btn ico-btn6">确定</a>';
				ts+='</div>';
			d_box.html(ts);
		}else if(obj.errorMsg){
			var ts = '<div class="tc">';
				ts+='<p class="dia-msg dia-msg-error">'+obj.errorMsg+'</p>';
				ts+='<a href="javascript:;" id="js_dia_btn" class="ico-btn ico-btn6">确定</a>';
				ts+='</div>';
			d_box.html(ts);
		}else if(obj.conFirm){
			var ts = '<div class="tc">';
				ts+='<p class="dia-msg dia-msg-error">'+obj.conFirm+'</p>';
				ts+='<a href="javascript:;"  rtype="ok" class="ico-btn ico-btn6 js_cf_btn">确定</a> <a href="javascript:;" rtype="reset" class="ico-btn ico-btn7 ml20 js_cf_btn">取消</a>';
				ts+='</div>';
			d_box.html(ts);
		}else{
			obj.fn();
			$('#dialog-box').html('');
		}
		
		//调用关闭方法
		$('#js_dia_btn').die().live('click',function(){
			dialog._close();
		});
		//确定和取消按钮...
		$('.js_cf_btn').die().live('click',function(){
			var type = $(this).attr('rtype');
			if(type == 'ok'){
				obj.fmCallBack(true);
				dialog._close();
			}else{
				obj.fmCallBack(false);
				dialog._close();
			}
		});
		
		//设置位置
		var set_center = function(obj){
			obj.css({
				left:($(window).width() - obj.width())/2+'px',
				top: ($(window).height()/2) - (obj.height()/2) + 'px'
			});
		};
		set_center(d_main);
		if(obj.top){
			d_main.css({
				top : obj.top + 'px'
			});
		}
		//设置背景层的高度
		d_bg.css({
			height: $('body').height()+ 'px'
		});
		if($('body').height() < $(window).height()){
			d_bg.css({
				height: $(window).height() + 'px'
			});
		}
		$(window).resize(function(){
			set_center(d_main);
			if(obj.top){
				d_main.css('top',obj.top+'px');
			}
		});
		d_bg.show().css({
			opacity:0,
			filter:'alpha(opacity=0)'
		}).animate({
			opacity:0.7
		},200);
		d_main.show().css({
			opacity:0,
			filter:'alpha(opacity=0)'
		}).animate({
			opacity:1
		},200);
		//关闭方法调用
		d_close.click(function(event){
			dialog._close();
			event.stopPropagation();
		});
		$(document).keydown(function(event){
			if(event.keyCode == 27){
				dialog._close();
			}
		});
		//拖拽
		M.darg($('#dialog-main')[0], $('#dialog-main .dialog-tit')[0]);
	},
	//关闭方法
	_close:function(){
		$('#dialog-bg').animate({
			opacity:0,
			filter:'alpha(opacity=0)'
		},150,function(){
			$('#dialog-bg').remove();
		});
		$('#dialog-main').remove();
	}
};

M.getId = function(id){
	return document.getElementById(id);
}



//公用调用
$(document).ready(function() {
	M.publicStyleOper();
	M.publicReturnTop();
	M.publicDownList();	
	M.publicYjfk();
});
