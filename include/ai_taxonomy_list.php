<?php
	global $wpdb;
	wp_enqueue_script('ai_multiupload_script', plugins_url( 'js/multi-message.js' , __FILE__ ) );
	wp_enqueue_style('ai_multiupload_css',  plugins_url('css/multi-image.css', __FILE__ ) );
?>
<div class="wrap" id="of_container">
	<div id="of-popup-save" class="of-save-popup" style="left: 250px; top: 96px;">
		<div class="of-save-save">Saved</div>
	</div>
	<img src="<?php echo plugins_url('images/augustinfotech.jpg', __FILE__ );?>" class="icon32" />
	<h2><?php _e('Multiupload Taxonomy Settings','aimultiuploadintaxonomy');?></h2>
	<form method="post" action="options.php" name="AIGolbalMultiuploadOptions" id="AIGolbalMultiuploadOptions">
		<?php 
		settings_fields( 'ai-taxonomy-fields' ); 
		$args = array(
		'public'   => true,
		'_builtin' => false
		); 
		$taxonomy_list= get_taxonomies( $args) ;
		?>
		<h3><?php _e('Custom Taxonomy List','aimultiuploadintaxonomy');?></h3>
		<table class="form-table" id="form-settings">
			<tbody>
				<tr>
					<th class="field-status"><?php _e('Status'); ?></th>
					<th class="field-name"><?php _e('Taxonomy Name'); ?></strong></th>
				</tr>
				<?php foreach($taxonomy_list as $value) { ?>
					<tr>
						<td class="field-status">
							<?php 
							$selected_taxonomy_list = get_option('ai_taxonomy_name'); 
							if(is_array($selected_taxonomy_list)) {
								$taxonomy = in_array($value, $selected_taxonomy_list);
							}
							?> 
							<input type="checkbox" name="ai_taxonomy_name[]" id="ai_taxonomy_name[]" value="<?php echo $value; ?>" <?php if(isset($taxonomy) && !empty($taxonomy)){echo "checked";} ?>  />
						</td>
						<?php $value = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value); ?>
						<td class="field-name"><strong><?php _e($value, 'aimultiuploadintaxonomy');?></strong></td>
					</tr>
				<?php } ?>
				<tr>
					<td>&nbsp;</td>
					<td><input class="button-primary" type="submit" value="<?php _e('Save All Changes','aimultiuploadintaxonomy');?>"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>