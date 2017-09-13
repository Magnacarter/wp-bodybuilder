/**
 * genPDF
 *
 * generate a pdf for the workout
 *
 * @since 1.0.0
 */
function genPDF() {

	var cardHeight = document.getElementById( 'wpbb-workout-card' ).offsetHeight;
	var a4         = [500, cardHeight];

	html2canvas( document.getElementById('wpbb-workout-card'), {

		onrendered: function( canvas ) {

			//document.getElementById('wpbb-workout-inner').parentNode.style.overflow = 'hidden';
			var img = canvas.toDataURL('image/png');
			doc = new jsPDF({
				unit: 'px',
				format: a4
			});
			doc.addImage( img, 'JPEG', 10, 10 );
			doc.save('workout.pdf');

		},
	});
}
//# sourceMappingURL=app.js.map
