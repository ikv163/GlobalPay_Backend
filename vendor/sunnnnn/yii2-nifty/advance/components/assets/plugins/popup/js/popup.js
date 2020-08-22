if (typeof $ === 'function') {
    $(function () {
        var popup = {
            defaultConfig: {
                width: 320,
                height: 170,
                timer: 0,
                type: 'info',
                showConfirmButton: true,
                showCancelButton: false,
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            },
            html: '<div class="popup_box">' +
            '<div class="popup_image"></div>' +
            '<div class="popup_title"></div>' +
            '<div class="popup_message"></div>' +
            '<div class="popup_button">' +
            '<button class="popup_cancel"></button>' +
            '<button class="popup_confirm"></button>' +
            '</div>' +
            '</div>',
            overlay: '<div class="popup_overlay"></div>',
            open: function (title, message, callback, o) {
                var opts = {}, that = this;
                $.extend(opts, that.defaultConfig, o);
                $('body').append(that.html).append(that.overlay);
                var box = $('.popup_box');
                box.css({
                    'width': opts.width + 'px',
                    'min-height': opts.height + 'px',
                    'margin-left': -(opts.width / 2) + 'px'
                });
                $('.popup_image').addClass(opts.type);
                title && $('.popup_title').html(title).show(),
                message && $('.popup_message').html(message).show();
                var confirmBtn = $('.popup_confirm'), cancelBtn = $('.popup_cancel');
                opts.showConfirmButton && confirmBtn.text(opts.confirmButtonText).show(),
                opts.showCancelButton && cancelBtn.text(opts.cancelButtonText).show();
                $('.popup_overlay').unbind('click').bind('click', function () {
                    that.close();
                });
                confirmBtn.unbind('click').bind('click', function () {
                    that.close();
                    typeof callback === 'function' && callback(true);
                });
                cancelBtn.unbind('click').bind('click', function () {
                    that.close();
                    typeof callback === 'function' && callback(false);
                });
                var h = box.height();
                box.css({
                    'margin-top': -(Math.max(h, opts.height) / 2 + 100) + 'px'
                });
            },
            close: function () {
                $(".popup_overlay,.popup_box").remove();
            }
        };
        window.showAlert = function (title, message, callback, opts) {
            popup.open(title, message, callback, opts);
        };
        var _confirm = window.confirm;
        window.showConfirm = function (title, message, callback, opts) {
            opts = $.extend({type: 'question', showCancelButton: true}, opts);
            if (typeof callback === 'function') {
                popup.open(title, message, callback, opts);
            } else {
                return _confirm(title);
            }
        };
        
        var popupTimerTemplate;
        
        window.showLoading = function(message) {
        	//hide message
        	if ($("#showMessageTemplate").length) {
        		$("#showMessageTemplate").fadeOut("fast");
        	}
        	message = typeof message == 'undefined' ? '加载中' : message;
        	var template = '<div id="showLoadingTemplate" class="weui_loading_toast" style="display:none;">' + 
        						'<div class="weui_mask_transparent"></div>' + 
        						'<div class="weui_toast">' + 
        							'<div class="weui_loading">' +  
        								'<div class="weui_loading_leaf weui_loading_leaf_0"></div>' +    
        								'<div class="weui_loading_leaf weui_loading_leaf_1"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_2"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_3"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_4"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_5"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_6"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_7"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_8"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_9"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_10"></div>' + 
        								'<div class="weui_loading_leaf weui_loading_leaf_11"></div>' + 
        							'</div>' + 
        							'<div class="weui_toast_content">' + message + '</div>' + 
        						'</div>' +
        					'</div>';
        	if (!$("#showLoadingTemplate").length) {
        		$("body").append(template);
        	} else {
        		$("#showLoadingTemplate .weui_toast_content").html(message);
        	}
        	$("#showLoadingTemplate").fadeIn("fast");
        };
        
        window.hideLoading = function(){
        	if ($("#showLoadingTemplate").length) {
        		$("#showLoadingTemplate").fadeOut("fast");
        	}
        }

        window.showMessage = function(message, duration) {
        	if ($("#showLoadingTemplate").length) {
        		$("#showLoadingTemplate").fadeOut("fast");
        	}
        	clearTimeout(popupTimerTemplate);
        	message = typeof message == 'undefined' ? 'Message' : message;
        	duration = typeof duration == 'undefined' ? 3000 : duration;
        	var template = '<div id="showMessageTemplate" style="display:none;">'+
                				'<div class="weui_msg">'+
                					'<span class="weui_msg_content">' + message + '</span>'+
                				'</div>'+
                			'</div>';
        	if (!$("#showMessageTemplate").length) {
        		$("body").append(template);
        	} else {
        		$("#showMessageTemplate .weui_msg_content").html(message);
        	}
        	$("#showMessageTemplate").fadeIn("fast");
        	popupTimerTemplate = setTimeout(function() {
        		if ($("#showMessageTemplate").length) {
            		$("#showMessageTemplate").fadeOut("fast");
            	}
            }, duration);
        };
        
        window.hideMessage = function(){
        	if ($("#showMessageTemplate").length) {
        		$("#showMessageTemplate").fadeOut("fast");
        	}
        }
        
        window.showNoty = function(message, duration, type){
        	if ($("#showLoadingTemplate").length) {
        		$("#showLoadingTemplate").fadeOut("fast");
        	}
        	
        	message = typeof message == 'undefined' ? 'Message' : message;
        	duration = typeof duration == 'undefined' ? 5000 : duration;
        	type = typeof type == 'undefined' ? 'info' : type;
        	// type: primary || info || success || warning || danger || mint || purple || pink ||  dark
        	$.niftyNoty({
                type: type,
                container: 'floating',
                message: '<label class="text-semibold">'+ message +'</label>',
                closeBtn: false,
                floating: {
                    position: "top-right",
                    animationIn: 'fadeInRight',
                    animationOut: 'fadeOutRight'
                },
                timer: duration,
            });
        }
        
        
        window.showInfo = function(message, duration){
        	showNoty(message, duration, 'info');
        }
        
        window.showSuccess = function(message, duration){
        	showNoty(message, duration, 'success');
        }
        
        window.showError = function(message, duration){
        	showNoty(message, duration, 'danger');
        }
        
        window.showWarning = function(message, duration){
        	showNoty(message, duration, 'warning');
        }
        
        window.showDanger = function(message, duration){
        	showNoty(message, duration, 'danger');
        }
        
        window.showMint = function(message, duration){
        	showNoty(message, duration, 'mint');
        }

    });
}
