jQuery(function($){
	$(document).ready(function(){
		if(tc_county_admin.cat != '') {
			if(tc_county_admin.cat == 'news') {
				$('#in-category_county-3').prop('checked',true);
			}
			else if(tc_county_admin.cat == 'events') {
				$('#in-category_county-4').prop('checked',true);
			}
			else if(tc_county_admin.cat == 'business'){
				$('#in-category_county-6').prop('checked',true);
			}
			else if(tc_county_admin.cat == 'obituaries') {
				$('#in-category_county-5').prop('checked',true);
			}
		}
	});
});