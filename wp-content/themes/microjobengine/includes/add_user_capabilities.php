<?php

/*
 * The unfiltered_upload permission is closely related to unfiltered_html. If this permission is given to a user, they can upload files are NOT on the WordPress whitelist:

Images: jpg, jpeg, png, gif, ico
Documents pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx
Audio: mp3, m4a, ogg, wav
Video: mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2
*/
function mje_add_role_caps() {
    // Gets the simple_role role object.
    $admin  = get_role( 'administrator' );
    $author = get_role( 'author' );
    $editor = get_role( 'editor' );
    // Add a new capability.
    $admin->add_cap( 'unfiltered_upload' );
    $author->add_cap( 'unfiltered_upload' );
    $editor->add_cap( 'unfiltered_upload' );
    // ALLOW_UNFILTERED_UPLOADS
    /**
     * add thid code to wp-config.php file
        define( 'ALLOW_UNFILTERED_UPLOADS', true );
     **/
}

// Add simple_role capabilities, priority must be after the initial role definition.
add_action( 'init', 'mje_add_role_caps', 19 );