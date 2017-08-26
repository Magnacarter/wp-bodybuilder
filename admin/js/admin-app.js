jQuery( document ).ready( function($) {

	// Counter for day-repeater click function
	var i = 1;

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
	 * Day repeater
	 *
	 * click function that adds a repeated day
	 */
	$( '.day-repeat-add' ).click( function() {

		i++;

		field = $( this ).closest( 'td' ).find( '.day_repeat li#day-list-item:last' ).clone( true );

		field.find( '#exercise-list li' ).remove();

		fieldLocation = $( this ).closest( 'td' ).find( '.day_repeat li#day-list-item:last' );

		$( '.day-header', field ).find( 'h4' ).html( 'Day ' + i, function( index, name ) {

			return name.replace( /(\d+)/, function( fullMatch, n ) {

				return Number(n) + 1;

			});

		});

		field.insertAfter( fieldLocation, $( this ).closest( 'td' ) );

		return false;

	});

	/**
	 * Select an exercise for workout
	 */
	var exerciseSelect = $( 'select.wpbb-exercise-selection' );

	exerciseSelect.change( function() {

		var selectedValue = $( this ).val();
		var list          = $( this ).parent();

		$( this ).prop('selectedIndex',0);

		//saveExercises.show();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'workout_process_ajax',
				exerciseId: selectedValue
			},
			success: function( response ) {

				if ( response.success === true ) {

					console.log( response.data.post_title );

					var json = response.data;

					if( json.post_title !== '' ) {
						list.find( '#exercise-list' ).append( '<li class="list" data-class-id="'+ json.ID +'"><strong>Exercise: </strong>' + json.post_title + '<span><a href="" class="remove-exercise">  Remove</a></span></li>' );
					}

					$( ".remove-exercise" ).on( 'click', function(e) {
						e.preventDefault();
						console.log( 'clicked' );
						var exerciseListItem = $( this ).parent().parent();
						exerciseListItem.remove();
					});
				}

				if ( response.success === false ) {
					console.log( 'not working' );
				}
			},
			error: function( xhr, status, error ) {
				var err = eval( "(" + xhr.responseText + ")" );
				console.log( err.Message );
			}
		});

	});

	/**
	 * Remove a repeated day
	 */
	$( '.day-repeat-remove' ).click( function(){

		$( this ).parent().parent().remove();

		return false;

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
//# sourceMappingURL=admin-app.js.map
