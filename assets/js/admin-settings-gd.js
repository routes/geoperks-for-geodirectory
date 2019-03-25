jQuery(document).ready(function(){
	
	
	
	jQuery("#a-update-from-provider-gd").on('click', function(){
	jQuery(this).find('.loadspinner').show();
   
});



jQuery('#a-update-from-provider-gd').click(function() {
	
		var obj_element = jQuery(this);
		obj_element.find('.loadspinner').show();
		
		jQuery.ajax({
			type:'POST',
			data:{
				action:'sync_ajax_call_gd'  
			},
			url: geoperksonclient_js_params.ajax_url,
			success: function(respose){
				
				obj_element.find('.loadspinner').hide();
				var res_data = JSON.parse(respose);
				if(res_data.status == 0) {
					alert(res_data.message);
				}else {
					window.location.reload();
				}
				 
			}
		});
	
});
	
	
	
});