(function($) {
$(window).load(function() {     // Has to be on window.load instead of document.ready because components are created in
                                // the client-side, after the DOM is ready.
    /**
     * Handle checkboxes
     */
    var svg_html = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="checkbox-checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg>';

    // We're only targeting the post-meta checkboxes. Not the ones with the Post's core data (like "Sticky post").
    var $checkboxes = $( '.edit-post-meta-boxes-area input[type="checkbox"]' );

    $checkboxes.change( function() {

        if ( this.checked ) {

            $(svg_html).insertAfter(this);

        } else {

            $(this).next( '.checkbox-checked' ).remove();
        }
    });

    /**
     * Handle the index field's visibility according to the value of the multi_page field.
     */
    var $index_field_wrapper = $( '._index_field' );

    $('input[name="_is_multi_page"]').change(function() {

        if ( this.checked ) {
            $index_field_wrapper.addClass( 'active' );
        } else {
            $index_field_wrapper.removeClass( 'active' );
        }
    });

    // Trigger on page load so that the checkboxes reflect their current state.
    $checkboxes.change();
});
}(jQuery, this));
