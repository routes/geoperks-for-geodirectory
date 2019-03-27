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
			url: geoperksforgd_js_params.ajax_url,
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



jQuery('.load-checkbox-perk-forgd').click(function() {
	var perk_status = 0;
	var status_text = 'disable';
	if(jQuery(this).is(':checked')) {
		perk_status = 1;
		status_text = 'enable';
	}
	var confirm_status = confirm('Are you sure you want to '+ status_text + ' this perk?' );
	if(confirm_status) {
		var perk_id = jQuery(this).attr('data-perk-id'); 
		jQuery.ajax({
			type:'POST',
			data:{
				action:'geoperksforgeodirectory-enable_disable_action',
				perk_id: perk_id,
				perk_status: perk_status  
			},
			url: geoperksforgd_js_params.ajax_url,
			success: function(respose) {
				/* var res_data = JSON.parse(respose);
                     obj_element.find('.form-content').html(res_data.html);*/
				// jQuery('#geo-perk-manage-'+perk_id).removeClass('show');

			}
		});
	}
});
	
	
	
});