jQuery(document).ready(function() {
		 
		 // Issue Image Prepare the variable that holds our custom media manager.
		 var loc_multi_image_file;
		 var locationlabel = 0;
	 
		 // Bind to our click event in order to open up the new media experience.
		 jQuery(document.body).on('click.mojoOpenMediaManager', '.multi-image-add', function(e){ //mojo-open-media is the class of our form button
			 // Prevent the default action from occuring.
			 e.preventDefault();
			 
			 var id = jQuery(this).attr("id")
	     	 var up_btn = id.split("-");
	    	 up_img_id = up_btn[1];
    	        
			// Get our Parent element
			 locationlabel = jQuery(this).parent();
			 
			 // If the frame already exists, re-open it.
			 if ( loc_multi_image_file ) {
			 loc_multi_image_file.open();
			 return;
			 }
			 
			 loc_multi_image_file = wp.media.frames.loc_multi_image_file = wp.media({
						 title: "Add Image",
					     button: {
						  text: "Insert Image",
					     },
						 editing:    true,
						 className: 'media-frame loc_multi_image_file',
						 frame: 'select', //Allow Select Only
						 multiple: false, //Disallow Mulitple selections
						 library: {
						 type: 'image' //Only allow images type: 'image'
				 },
			});
			
			loc_multi_image_file.on('select', function(){
			 // Grab our attachment selection and construct a JSON representation of the model.
			 	var attachment = loc_multi_image_file.state().get('selection').first().toJSON();
			 
				 if(attachment.subtype == "pdf" || attachment.subtype == "doc" || attachment.subtype == "docx" || attachment.subtype == "txt")
				 {
					alert("Please Insert only image");
					return;
				 }
			 
				 if(typeof(attachment.sizes.thumbnail) != "undefined")
				 	var thum_url = attachment.sizes.thumbnail.url;
				 else	
				 	var thum_url = attachment.sizes.full.url;
				
			 	 var thumb_id = attachment.id; 
			 
				 // Send the attachment URL to our custom input field via jQuery.
				 loc_url = attachment.url;
				 locurls = loc_url.substr( (loc_url.lastIndexOf('.') +1) );
						
				 if(locurls !='pdf' && locurls !='zip' && locurls !='rar' && locurls != "doc" && locurls != "docx" && locurls != "txt")
				 {
					jQuery('#image-'+up_img_id).val(thumb_id);
					
			        var image_src_live = "<img src='"+thum_url+"' height='150' width='150'><br/><br/><br/>";
					jQuery('#live-image-'+up_img_id).append(image_src_live);
					jQuery('#Upload_button-'+up_img_id).hide();
			    }
				else
				{
				 	alert('Please add only image');
			    }
			 
			 });
	 
			// Now that everything has been set, let's open up the frame.
			 loc_multi_image_file.open();
		 });
		 
	jQuery('.multi-image-remove').live( "click", function(e) {
		e.preventDefault();
		var id = jQuery(this).attr("id")
		var div_id = id.split("-");
		var row_id = div_id[1];
		jQuery("#row-"+row_id ).remove();
	});
	
	jQuery(document).ajaxSuccess(function(event, xhr, settings) {
	 	if ( settings.data.indexOf("action=add-tag") !== -1 ) {
	 		jQuery('#sr_multi_images').css('display','none');
	 		jQuery('.multi-outer-div').remove();
		 	jQuery( "#sr_multi_images input[type=hidden]" ).each(function( i ) {
		 		jQuery( "#sr_multi_images input[type=hidden]" ).attr('value','');
		 	});
	 	}
    });	
});

function addNewRow(thumbid)
{ 
	if(typeof(thumbid)==='undefined') thumbid = "";
	
	totalItems += 1;
    
    var bulkImages = '<div class="multi-outer-div" id=row-'+totalItems+'> <div class="multi-inner-div" id="live-image-'+totalItems+'"></div><input style=\'width:70%\' id=image-'+totalItems+' type=\'hidden\' name=\'sr_multi_images['+totalItems+']\' value=\''+thumbid+'\' />'
    +'<input id=\'Upload_button-'+totalItems+'\' type=\'button\' value=\'Upload\' class=\'multi-image-add button button-primary\' />'
    +'<input id=\'remove-'+totalItems+'\' type=\'button\' value=\'Remove\' class=\'multi-image-remove button button-primary\' /></div>';
	jQuery('#sr_multi_images').css('display','block');
	jQuery('#sr_multi_images').append(bulkImages);
}