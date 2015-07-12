var tc_county_settings = {
	inside_county: false,
	county_id:0
};
jQuery(function($){
	
	
	$(document).ready(function(){

		get_session('current_county');
		
		
		/* check if ads exist */
		if($('.uiwelcome').length > 0) {
			$('.uiwelcome').html('');	
		}
		var menu = '<i id="toggle-new-ads" data-toggle="modal" data-target="#adsNew" class="icon ion-plus-circled">Place new ad</i>';
		
		
		$('.awpcp-menu-items').each(function(i,obj){
				$(this).html(menu);		
			
		});
		
		$('.modal .awpcp-menu-items').each(function(i,obj){
			$(this).html('');
		});
		
		if(tc_county.ads_new_progress == true){
			$('#toggle-new-ads').trigger('click');
		}
		
		
		if($('.changecategoryselect').length > 0 ){
			$('.changecategoryselect form').attr('action',tc_county.ads_browsecat);	
		}
	});
	
	window.dropdown_list = function(e,obj) {
		e.preventDefault();
		$(obj).next('.sub-menu').slideToggle();
		
	};
	
	window.updateresponse = function() {
		alert(tc_county_settings.county_id);
	};
	
	window.set_session = function(session,val) {
			var data = {
				'action' : 'set_session',
				'session' : session,
				'value' : val,
			};
		
			$.post(tc_county.ajax_url, data, function(response){
				return true;
			});
		return false;
	};
	
	window.set_session_href = function(e,obj,session,val) {
		e.preventDefault();
			var data = {
				'action' : 'set_session',
				'session' : session,
				'value' : val,
			};
			console.log($(obj).attr('href'));
			$.post(tc_county.ajax_url, data, function(response){
				window.location = $(obj).attr('href');
			});
		return false;
	};
	
	window.get_session = function(session) {
		var data={
			'action': 'get_session',
			'session' : session
		};
		$.post(tc_county.ajax_url,data,function(response){
			if(session == 'current_county') {
				if(response) {
					tc_county_settings.county_id = response;
				}
				else {
					tc_county_settings.county_id = 0;
				}
			}
		});
		
	};
	
	
	//Extend tc_county.
	tc_county.change_nav = function() {
		if(tc_county_settings.inside_county == true) { // proceed to changing menu to menu items
			var mainmenu = '#' + tc_county.menu_id;
			var menu = "";
			for(var key in tc_county.menu) {
				if(typeof tc_county.menu[key] == 'string') {
					console.log(tc_county.menu[key]+' added to menu');
					menu += "<li class=\"menu-item\"><a href=\""+tc_county.menu[key]+"\">"+key+"</a>";
				}
				else {
					for(var key2 in tc_county.menu[key]) {
						if(key2 == 'main-link') {
							menu += "<li class=\"menu-item\"><a onclick=\"dropdown_list(event,this);\" href=\"#\">"+key+"</a>";
							menu +='<ul class="sub-menu">';
						}
						else {
							console.log('The 2nd hierarchy is: '+tc_county.menu[key][key2]);
							menu += "<li class=\"menu-item\"><a href=\""+tc_county.menu[key][key2]+"\">"+key2+"</a></li>";
						}
					}
					menu += '</ul></li>';
				}
			}
			
			
			$(mainmenu).html(menu);
		}
	};
});