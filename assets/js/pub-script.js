jQuery( document ).ready( function($) {

	var btn      = $( '#instruction-btn' ),
		closeBtn = $( '.close-btn' ),
		popUp    = $( '.instruction-popup' );

	$( '.instruction-btn' ).on( 'click', function( e ) {

		$(this).parent().parent().find( ".instruction-popup" ).show(200);

		console.log('click');

	});

	closeBtn.on( 'click', function( e ) {

		$(this).parent().parent().hide(200);

	});

});

/**
 * genPDF
 *
 * generate a pdf for the workout
 *
 * @since 1.0.0
 */
function genPDF() {

	var cardHeight = document.getElementById('wpbb-workout-card').offsetHeight;
	var a4 = [500, cardHeight];

	html2canvas(document.getElementById('wpbb-workout-card'), {

		onrendered: function (canvas) {

			//document.getElementById('wpbb-workout-inner').parentNode.style.overflow = 'hidden';
			var img = canvas.toDataURL('image/png');
			doc = new jsPDF({
				unit: 'px',
				format: a4
			});
			doc.addImage(img, 'JPEG', 10, 10);
			doc.save('workout.pdf');

		}
	});
}