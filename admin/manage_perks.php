	<div class="geop-content-div">

        <?php 
				global $wpdb;
				
				$active_perks = '';        
                $installed_perks = '';   
                $installed_perks_versions = '';
                $user_perks = '';
				
				
             ?>
             <style>
                .geodir-save-button {
                    display: none !important; 
                }
				table:nth-child(odd) {
				  background: #f1f1f1;
				}

				
            </style>
			<table class="wp-list-table widefat fixed striped gd-cpts">
			<tbody>
                  <tr>
				  <td>
				  <a id="a-update-from-provider-gd" href="javascript:void(0);"  ><button id="update-from-provider-gd" type="button"  title="Sync" class="sync-button" ><?php echo __( "Sync",  GEOPERKSFORGD_TEXT_DOMAIN) ?></button><label class="loadspinner" style="display:none;" ><img src="<?php echo admin_url('images/loading.gif'); ?>"  ></label></a>
				  </td>
				  </tr>
			</table>
             <table class="wp-list-table widefat fixed striped gd-cpts">
                        <tbody>
                            <tr>
                                <th width="50"><?php echo __('Sr. No.', GEOPERKSFORGD_TEXT_DOMAIN);?></th>
                                <th><?php echo __('Perk', GEOPERKSFORGD_TEXT_DOMAIN);?></th>
                                <th><?php echo __('Enabled', GEOPERKSFORGD_TEXT_DOMAIN);?></th>
                                <th><?php echo __('Description', GEOPERKSFORGD_TEXT_DOMAIN);?></th>
                                <th><?php echo __('Docs', GEOPERKSFORGD_TEXT_DOMAIN);?></th>
                               
                            </tr>
                            <?php
                              $row_num = 1;
                            $default_version = '1.0';
							
							
							$querystr = "SELECT * FROM $wpdb->posts WHERE post_type LIKE 'geoperksforgd' ORDER BY ID ASC";

							$user_perks = $wpdb->get_results($querystr);
							
							//echo "<pre>";
							//print_r($user_perks);
							
							$geoperk_default =GeoperksForGeodirectoryFeatures::perkforgd_default();
							 
							 
							 foreach( $geoperk_default as $def) { ?>
							 
							 
							 <tr>
                                <td><?php echo $row_num; ?></td>
                                <td><?php echo $def['post_title']; ?></td>
								<td><?php
								
							 	$perksChk = get_option('geoperksforgd_list_'.$def['ID'], true);
							
								$chk='';
								if($perksChk==1) {
									$chk='checked';
								}
									echo '<input type="checkbox"  class="load-checkbox-perk-forgd" data-perk-id="'.$def['ID'].'" '.$chk.' >';  ?>
								</td>
                                <td><?php echo $def['post_content']; ?></td>
								<td><?php echo 'Version: '.$def['perk_version'].'<br><a href="'. $def['perk_url'].'" target="_blank">'. __('View More', GEOPERKSFORGD_TEXT_DOMAIN).'</a>'; ?></td>  
								
								
                            </tr>
								 
							
							<?php $row_num++;  }
							 
							
                            foreach($user_perks as $perk) {
								
								$perkisfree =  get_post_meta($perk->ID,'meta_is_free',true);
								if($perkisfree==1){
									continue;
								}
								
								$perk_url =  get_post_meta($perk->ID,'meta_perk_url',true);
								$perk_version =  get_post_meta($perk->ID,'meta_perk_version',true);
		
								if(empty($perk_version)){
									$perk_version ="1.0";
								}
                               
                            ?>
                            <tr>
                                <td><?php echo $row_num; ?></td>
                                <td><?php echo $perk->post_title; ?></td>
								
								<td>&nbsp;</td>
                                <td><?php echo $perk->post_content; ?></td>
								<td><?php echo 'Version: '.$perk_version.'<br><a href="'. $perk_url.'" target="_blank">'. __('View More', GEOPERKSFORGD_TEXT_DOMAIN).'</a>'; ?></td>  
								
								
                            </tr>
							
                              
							
                           
                            <?php $row_num++; } ?>
                       </tbody>
                </table>

        </div>