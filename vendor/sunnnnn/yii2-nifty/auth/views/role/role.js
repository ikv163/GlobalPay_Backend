$(function(){
	var init = function(){
		search('avaliable');
		search('assigned');
	}
	
	var search = function(target){
		var $list = $('select.list[data-target="' + target + '"]');
	    $list.html('');
	    var q = $('.search[data-target="' + target + '"]').val();
	
	    $.each(_opts[target], function (route, value) {
	        if (value.label.indexOf(q) >= 0) {
				$list.append($('<option>').text(value.label).val(value.id).data('route', route));
	        }
	    });
	}
	
	$('.search[data-target]').keyup(function () {
	    search($(this).data('target'));
	});
	
	$('.btn-assigned').click(function () {
	    var $this = $(this);
	    var assigned_list = $('select.list[data-target="assigned"]');
	    var avaliable_list = $('select.list[data-target="avaliable"]');
		
		var options = avaliable_list.find('option:selected');
		
		if (options && options.length) {
			$.each(options, function(){
				var route = $(this).data('route');
				var id  = $(this).val();
				var label  = $(this).html();
				delete _opts.avaliable[route];
				_opts.assigned[route] = {'id':id, 'label':label};
			});
		}
		console.log(_opts);
		init();
	});
	
	$('.btn-avaliable').click(function () {
	    var $this = $(this);
	    var assigned_list = $('select.list[data-target="assigned"]');
	    var avaliable_list = $('select.list[data-target="avaliable"]');
		
		var options = assigned_list.find('option:selected');
		
		if (options && options.length) {
			$.each(options, function(){
				var route = $(this).data('route');
				var id  = $(this).val();
				var label  = $(this).html();
				delete _opts.assigned[route];
				_opts.avaliable[route] = {'id':id, 'label':label};
			});
		}
		console.log(_opts);
		init();
	});
	
	
	$('.route-form-submit').click(function(){
    	var that = $(this);
		var form = $('.route-form');
		$('select.list[data-target="assigned"]').find('option').each(function(){
			$(this).attr('selected', 'selected');
		});
    	showLoading(_message.loading);
		$.ajax({
	        type: 'post',
	        url: form.data('action'),
	        data: form.serialize(),
	        dataType: 'json',
	        error: function(xhr) {
	        	hideLoading();
				if(xhr.status == '403'){
					showError(_message.errorHttp403);
				}else if(xhr.status == '404'){
					showError(_message.errorHttp404);
				}else{
					showError(_message.errorHttp500);
				}
	        },
	        success: function(result) {
	        	hideLoading();
		        if(result.result){
					if(result.result === true || result.result == 1){
						showError(result.message);
					}
					if(result.result == '@'){
						history.back();
					}else if(result.result.substr(0, 1) == '/'){
			        	location.href = result.result;
					}
		        }else{
		        	showError(result.message);
		        }
	        }
	    });
	});
	
	
	init();
});
