jQuery( document ).ready( function($) {

	// Set all variables to be used in scope
	var frame,
		metaBox = $('#exercise-custom-fields.postbox'), // Your meta box id here
		addImgLink = metaBox.find('.upload-custom-img'),
		delImgLink = metaBox.find( '.delete-custom-img'),
		imgContainer = metaBox.find( '.custom-img-container'),
		imgIdInput = metaBox.find( '.custom-img-id' );

	// ADD IMAGE LINK
	addImgLink.on( 'click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Select or Upload Media for Exercise',
			button: {
				text: 'Use this media'
			},
			multiple: true  // Set to true to allow multiple files to be selected
		});

		// When an image is selected in the media frame...
		frame.on( 'select', function() {

			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();

			// Send the attachment URL to our custom image input field.
			imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:150px;"/>' );

			// Send the attachment id to our hidden input
			imgIdInput.val( attachment.id );

			// Hide the add image link
			addImgLink.addClass( 'hidden' );

			// Unhide the remove image link
			delImgLink.removeClass( 'hidden' );
		});

		// Finally, open the modal on click
		frame.open();
	});


	// DELETE IMAGE LINK
	delImgLink.on( 'click', function( event ){

		event.preventDefault();

		// Clear out the preview image
		imgContainer.html( '' );

		// Un-hide the add image link
		addImgLink.removeClass( 'hidden' );

		// Hide the delete image link
		delImgLink.addClass( 'hidden' );

		// Delete the image id from the hidden input
		imgIdInput.val( '' );

	});

	/**
	 * Repeater field
	 *
	 * click function that adds a repeated field
	 */
	$( '.repeatable-add' ).click( function() {

		field = $( this ).closest( 'td' ).find( '.custom_repeatable li:last' ).clone( true );

		fieldLocation = $( this ).closest( 'td' ).find( '.custom_repeatable li:last' );

		$( 'input', field ).val('').attr( 'name', function( index, name ) {

			return name.replace( /(\d+)/, function( fullMatch, n ) {

				return Number(n) + 1;

			});

		});

		field.insertAfter( fieldLocation, $( this ).closest( 'td' ) );

		return false;

	});

	/**
	 * Remove a repeated field
	 */
	$( '.repeatable-remove' ).click( function(){

		$( this ).parent().remove();

		return false;

	});

	/**
	 * Sort repeated fields and save new order to db
	 * with a php callback function
	 */
	$( '.custom_repeatable' ).sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.sort',
		update: function (event, ui) {

			var list = $( this ).sortable( "toArray", {attribute: 'id'} );
			var data = [];

			for( var i = 0; list.length > i; i++ ) {

				var item    = list[i];
				var itemVal = $( "#" + item ).find( 'input' ).val();

				data.push( itemVal );

			}

			$.ajax({
				type: 'POST',
				datatype: 'json',
				url: ajaxurl,
				data: {
					action: 'process_ajax',
					data: data,
					postId: $( '#exercise_repeatable-repeatable' ).attr('data-post-id')
				},
				success: function( response ) {
					if ( response.success === true ) {

						console.log('working');
						console.log( response.data );

					}
					if ( response.success === false ) {

						console.log( response );

					}
				}
			});
		}
	});

});