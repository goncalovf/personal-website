(function($) {
$(document).ready(function() {

	var openers = $( '.index .i-chevron-right' );

	openers.click( function() {

		var ul  = this.parentElement.querySelector("ul:first-of-type");

		if( this.classList.contains('active') ) {

			this.classList.remove('active');
			ul.classList.add('closed');

		} else {

			this.classList.add('active');
			ul.classList.remove('closed');
		}
	});
});
}(jQuery, this));
