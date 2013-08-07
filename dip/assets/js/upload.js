// Uploading files
var file_frame;
 
  jQuery('.upload_image_button').live('click', function( event ){
 
    event.preventDefault();
 
    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }
 
    // Create the media frame.
    

    
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      toolbar: false,
      library : { query: {post_parent: 0} }, query: {post_parent: 0},
      //state: 'insert',
      AttachmentView: 'uploaded',
      frame: 'select',
      multiple: false  // Set to true to allow multiple files to be selected
    });
 
 console.log(wp.media());
 
    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
 console.log(attachment);
      // Do something with attachment.id and/or attachment.url here
    });
 
    // Finally, open the modal
    file_frame.open();
  });