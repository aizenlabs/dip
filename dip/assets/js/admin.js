jQuery(document).ready(function($)
{	
  // orbit slider admin
  jQuery('#orbit-add').live('click', function(e)
  {
    e.preventDefault();

    $current = jQuery('.orbit-form').last();
    index = $current.data('index') + 1;

    $new = $current.clone();	
    $new.removeAttr('data-index')
        .attr('data-index', index);

    jQuery('h3', $new).text(function(i,val)
                {
                  if( /\d/.test(val) ) return val.replace(/\d+/, index);
                });

    jQuery($new).find('input, textarea')
                .val('')
                .attr('name', function(i,val)
                {
                  if( /\d/.test(val) ) return val.replace(/\d+/, index);
                });

    jQuery($current).after($new);
  });






	// social links
	jQuery('#one-sl-add').live('click', function(e){
		e.preventDefault();
		
		$current = jQuery('.one-sl-item').last();
		index = $current.data('index') + 1;
		
		$new = $current.clone();	
		$new.removeAttr('data-index').data('index', index);
		
		jQuery( $new ).find('input, select').val('').attr('name', function(i,val) {
		    if( /\d/.test( val ) ) {
		        return val.replace(/\d+/, index);
		    }
		});
		
		jQuery( jQuery('.one-sl-item').last() ).after( $new ).after('<br />');
		
	});

	// color fields	 
	jQuery('.color-field').wpColorPicker();
	
	// image uploads
	jQuery('.image-upload').live('click',function() {
		var $input = jQuery(this).prev();
		tb_show('Upload Image', 'media-upload.php?type=image&amp;TB_iframe=false');

		window.send_to_editor = function(html) {
			imgurl = jQuery('img', html).attr('src');
			$input.val(imgurl);
			tb_remove();
		}
		return false;
	});
});