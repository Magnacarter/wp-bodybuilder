jQuery( document ).ready( function($) {

	// Counter for day-repeater click function
	var i            = 1,
		select       = $( 'select.wpbb-exercise-selection' ),
		dayRepeatAdd = $( '.day-repeat-add' );

	// Set all variables to be used in scope
	var frame,
		metaBox      = $('#exercise-custom-fields.postbox'), // Your meta box id here
		addImgLink   = metaBox.find('.upload-custom-img'),
		delImgLink   = metaBox.find( '.delete-custom-img'),
		imgContainer = metaBox.find( '.custom-img-container'),
		imgIdInput   = metaBox.find( '.custom-img-id' );

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
	 * Add exercise
	 */
	var addExercise = function( selectedValue, list ) {

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'workout_process_ajax',
				exerciseId: selectedValue
			},
			success: function(response) {

				if (response.success === true) {
					var json = response.data;

					if (json.post_title !== '') {
						list.find('#exercise-list').append('<li class="list" data-class-id="' + json.ID + '"><strong>Exercise: </strong>' + json.post_title + '<span><a href="" class="remove-exercise">  Remove</a></span></li>');
					}

					$(".remove-exercise").on('click', function (e) {
						e.preventDefault();
						var exerciseListItem = $(this).parent().parent();
						exerciseListItem.remove();
					});
				}

				if ( response.success === false ) {
					console.log( 'not working' );
				}
			}
		});
	};

	/**
	 * Add day
	 */
	const addDay = function( bool ) {

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'add_day_ajax',
				addDay: bool
			},
			success: function( response ) {

				if (response.success === true) {

					var exercisePosts = response.data;
					var dayRepeat     = $( '.day_repeat' );

					dayRepeat.append( '<li id="day-list-item"><div class="day-header">' +
						'<span><h4>Day 1</h4></span>' +
						'</div>' +
						'<div><p>Add Exercises</p></div>' +
						'<select class="wpbb-exercise-selection">' +
						'<option>Add Exercise</option>' );

					for( var j = 0; exercisePosts.length > j; j++ ) {

						$( '.wpbb-exercise-selection' ).append( '<option value="' + exercisePosts[j].ID + '">' + exercisePosts[j].post_title + '</option>' );

					}

					dayRepeat.find( 'li' ).append( '</select>' +
						'<div class="selected-exercises">You\'ve added the following exercises:</div>' +
						'<div class="selected-exercises-wrap"><ul id="exercise-list"></ul></div>' +
						'<div id="remove-btn">' +
						'<a class="day-repeat-remove button" href="#">Remove Day</a></div>' );

					dayRepeat.append( '</li>' );

				}
			},
			complete: function() {
				removeDay();

				$('.wpbb-exercise-selection').change( function () {

					var selectedValue = $(this).val();
					var list          = $(this).parent();

					// Reset the dropdown menu so you can select the same item twice without selecting another first
					$( this ).prop( 'selectedIndex', 0 );

					addExercise( selectedValue, list );

				});
			}
		});
	};

	/**
	 * Remove day
	 */
	const removeDay = function() {

		$( '.day-repeat-remove' ).click( function( e ) {
			e.preventDefault();
			$( this ).parent().parent().remove();
			i--;
			return false;
		});
	};
	removeDay();

	// Add day on click
	dayRepeatAdd.click( function( e ) {

		e.preventDefault();
		i++;

		if( ! $( '.day_repeat' ).has( '.wpbb-exercise-selection' ).length ) {
			bool = true;
			addDay(bool);
		}

		field = $(this).closest('td').find('.day_repeat li#day-list-item:last').clone(true);

		field.find('#exercise-list li').remove();

		fieldLocation = $(this).closest('td').find('.day_repeat li#day-list-item:last');

		$('.day-header', field).find('h4').html('Day ' + i, function (index, name) {

			return name.replace(/(\d+)/, function (fullMatch, n) {

				return Number(n) + 1;

			});

		});

		field.insertAfter(fieldLocation, $(this).closest('td'));

		return false;

	});

	// Select an exercise for workout
	select.change( function () {

		var selectedValue = $(this).val();
		var list          = $(this).parent();

		$(this).prop( 'selectedIndex', 0 );

		addExercise( selectedValue, list );

		//saveExercises.show();

	});

});
//# sourceMappingURL=admin-app.js.map
