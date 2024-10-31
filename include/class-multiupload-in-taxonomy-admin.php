<?php 
/**
 * Plugin Name.
 *
 * @package   Multiupload_In_Taxonomy_Admin
 * @author    augustinfotech <http://www.augustinfotech.com/>
 * @license   GPL-2.0+
 * @link      http://www.augustinfotech.com
 * @copyright 2014 August Infotech
 */
// don't allow this file to be loaded directly
if ( !function_exists( 'is_admin' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Plugin_Name_Admin
 * @author  Your Name <email@example.com>
 */

class Multiupload_In_Taxonomy_Admin { 

	function __construct() {

		/**
		* Register taxonomy field
		*
		* Function Name: ai_register_taxonomy_fields
		*
		* @access public
		* @param 
		*
		* @created by Payal Patel and 07/04/2014
		**/
		add_action('admin_init', array( $this,'ai_register_taxonomy_fields' ));
		
		/**
		* Create admin menu page
		*
		* Function Name: ai_multiupload_setting
		*
		* @access public
		* @param 
		*
		* @created by Payal Patel and 07/04/2014
		**/
		add_action('admin_menu',array( $this,'ai_multiupload_setting'));
		
		global $checked_taxonomy_list;
		$checked_taxonomy_list = get_option('ai_taxonomy_name');
		
		add_action('init', array( $this,'ai_taxonomy_load' ));
		
	}
	
	function ai_register_taxonomy_fields()
	{  
		global $wpdb;
		register_setting( 'ai-taxonomy-fields', 'ai_taxonomy_name' ); 
		$wpdb->taxonomymeta = "{$wpdb->prefix}taxonomymeta";
	}
		
	function ai_multiupload_setting()
	{
		add_options_page(__('AI Multiupload In Taxonomy','aimultiuploadintaxonomy'), __('AI Multiupload In Taxonomy','aimultiuploadintaxonomy'), 'manage_options', 'ai_multiuploadtaxonomy', array( $this,'ai_multiupload_settings'));
		
		//add_menu_page(__('AI Multiupload In Taxonomy','aimultiuploadintaxonomy'),__('AI Multiupload In Taxonomy','aimultiuploadintaxonomy'),'manage_options','ai_multiuploadtaxonomy',array( $this,'ai_multiupload_settings'));
	}
	
	/**
	* callback function to create form for selection of custom taxonomy.
	*
	* Function Name: ai_multiupload_setting
	*
	* @access public
	* @param 
	*
	* @created by Payal Patel and 07/04/2014
	**/
	function ai_multiupload_settings()
	{
		include MT_PDIR_PATH."include/ai_taxonomy_list.php";
	}
	
	function ai_taxonomy_load()
	{
		global $checked_taxonomy_list;
		//$checked_taxonomy_list = get_option('ai_taxonomy_name');
		
		if(is_array($checked_taxonomy_list))
		{
			foreach($checked_taxonomy_list as $value)
			{ 
			
				/* For add - Start */
				add_action( $value.'_add_form_fields', array( $this,'add_image_taxonomy'), 10, 1 );
				/* For add - End */
				
				/* For edit - Start */
				add_action( $value.'_edit_form_fields', array( $this,'edit_image_taxonomy'),10,2);
				/* For edit - End */
				
				/* save in table - Start*/
				add_action( 'create_'.$value, array( $this,'save_image_taxonomy_meta'), 10, 2 );
				add_action( 'edited_'.$value,  array( $this,'save_image_taxonomy_meta'), 10, 2 );
				
				/* save in table - End */
				
				/* delete - Start */
				add_action( 'delete_'.$value,  array( $this,'delete_image_taxonomy'), 10, 2 );
				/* delete - End */
			}
		}
	}
	/*====================================== Add multiupload field for selected custom taxonomy Start ================================================ */

	function add_image_taxonomy($value)
	{
	
		$JS = "<script>totalItems= 0;var plugin_dir = '".plugin_dir_path(__FILE__ )."';";
		$JS .="</script>";
	    echo $JS;
	    
	    wp_enqueue_media();
		wp_enqueue_style('ai_multiupload_css',  plugins_url('css/multi-image.css', __FILE__ ) );
		wp_enqueue_script('ai_multiupload_script', plugins_url( 'js/multi-image.js' , __FILE__ ) );
		
		$image_nonce = wp_create_nonce( $value.'-image-nonce' );
		
		// this will add the custom meta field to the add new term page
		?>
			<div class="form-field">
				<input type="hidden" name="<?php echo $value; ?>-image_wpnonce" value="<?php _e($image_nonce,$value.'-image');?>">	
				<input type="hidden" name="taxonomy-name" value="<?php echo $value; ?>">
						 
				<label for="<?php echo $value; ?>-image"><?php _e( 'Multiupload Image','aimultiupload' ); ?></label>
		        <div id="sr_multi_images"></div>
		        <div style="clear:both;"> 
			     	<input class="multi-image-add1 button button-primary" type="button" onclick="addNewRow()" value="<?php _e( 'Add Image','aimultiupload' ); ?>" style="margin-top:5px;">
			    </div>
			</div>
		    <?php
	}
	function edit_image_taxonomy($tag,$value)
	{ 
		/*$JS = "<script>totalItems= 0;var plugin_dir = '".get_bloginfo('template_url')."';";
		$JS .="</script>";
	    echo $JS;*/
	    
	    $term_id = $value.'_term_id';
	 	$$term_id = $tag->term_id;
	 	$image_ids = $value.'_image_ids';
		$total = 0;
		$$image_ids = get_metadata('taxonomy', $$term_id, $value.'_image', true) ;
		if($$image_ids != '')
		{
			$total = (count($$image_ids)-1);
		}
        
        $JS = "<script>totalItems=".$total.";var plugin_dir = '".get_bloginfo('template_url')."';";
        $JS .="</script>";
        echo $JS;
		

		wp_enqueue_media();
		wp_enqueue_style('ai_multiupload_css',  plugins_url('css/multi-image.css', __FILE__ ) );
		wp_enqueue_script('ai_multiupload_script', plugins_url( 'js/multi-image.js' , __FILE__ ) );

		$image_nonce = wp_create_nonce( $value.'-image-nonce' );
	 		
	 	$term_id = $value.'_term_id';
	 	$$term_id = $tag->term_id;
	 	$image_ids = $value.'_image_ids';
		$$image_ids = get_metadata('taxonomy', $$term_id, $value.'_image', true) ;	
		
		?>
		<input type="hidden" name="<?php echo $value; ?>-image_wpnonce" value="<?php _e($image_nonce,$value.'-image');?>">
		<input type="hidden" name="taxonomy-name" value="<?php echo $value; ?>">
		
	   <tr class="form-field">
	   		<th valign="top" scope="row"><?php _e( 'Multiupload Image','aimultiupload' ); ?></th>
			<td>
			 	 <div id="sr_multi_images">
				 	<?php
				 	$i = 0;
				 	if(!empty($$image_ids))
				 	{
						foreach($$image_ids as $value1)
					 	{
					 		$image_path = wp_get_attachment_image_src($value1,'thumbnail'); 
					 		?>
					 		<div class="multi-outer-div" id="row-<?php echo $i; ?>" >
					 			<div class="multi-inner-div" id="live-image-<?php echo $i; ?> ">
					 				<img src="<?php echo $image_path[0]; ?>" height="150" width="150"><br/><br/><br/>
					 			</div>
					 			<input id="image-<?php echo $i; ?>" type="hidden" name="sr_multi_images[<?php echo $i; ?>]" value="<?php _e($value1,'aimultiupload');?>" />
					 			<input id="remove-<?php echo $i; ?>" type="button" value="<?php _e( 'Remove','aimultiupload' ); ?>" class="multi-image-remove-edit multi-image-remove button button-primary" />
					 		</div>
					 		<?php
					 		$i++;
					 	}
				 	}
				 	?>
				 </div>
		
		         <div style="clear:both;"> 
			     	<input class="multi-image-edit1 button button-primary" type="button" onclick="addNewRow()" value="<?php _e( 'Add Image','aimultiupload' ); ?>" style="margin-top:5px">
			     </div>
			</td>
	   </tr>	
		<?php	
	}
	function save_image_taxonomy_meta( $term_id )
	{	  	
			global $checked_taxonomy_list;
			$value = $_REQUEST['taxonomy-name'];
			
		  	$image_nonce = $_REQUEST[$value.'-image_wpnonce'];	
		  	
		  	if(! wp_verify_nonce( $image_nonce, $value.'-image-nonce' ))
				return;
				
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				  $tax_name = $_POST['taxonomy'];
				  $tax_obj  = get_taxonomy($tax_name);
			} else {
				  $tax_name = get_current_screen()->taxonomy;
				  $tax_obj  = get_taxonomy($tax_name);
			}
						
			if ( !current_user_can( $tax_obj->cap->edit_terms ) )
				return $term_id;
			
			if($_POST['sr_multi_images'])
			{
				$image_to_save = array();
				foreach($_POST['sr_multi_images'] as $imageid)
				{
					if(!empty($imageid))
						$image_to_save[] = $imageid;
				}
			}
			
			if(in_array($_REQUEST['taxonomy'],$checked_taxonomy_list))
				update_metadata('taxonomy', $term_id, $value.'_image', $image_to_save); 
	}
	
	
	function delete_image_taxonomy($term_id)
	{
		delete_metadata('taxonomy', $term_id, $_REQUEST['taxonomy'].'_image');
	}
	
/*====================================== Add multiupload field for selected custom taxonomy End ================================================ */
}