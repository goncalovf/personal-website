(function($) {
$(document).ready(function() {

	var nav = $( 'nav.nav-drill' );

	/**
	 * Handle menu toggle
	 */
	$( '#nav-h-menu' ).on( 'click', function() {

		$('body').toggleClass('nav-is-toggled')
	});

	nav.find( '.i-close' ).on( 'click', function() {

		$('body').removeClass('nav-is-toggled');
	});

	var navExpand = [].slice.call(document.querySelectorAll('.nav-expand'));

	navExpand.forEach(function($item) {
		$item.querySelector('.nav-link').addEventListener('click', function(e) {

			$(this).parent( '.nav-expand' ).addClass('active'); });

		$item.querySelector('.nav-back-link').addEventListener('click', function(item) {

			$(this).closest( '.nav-expand' ).removeClass('active')
		});
	});
});
}(jQuery, this));

