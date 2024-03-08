<?php
global $wp_query;
get_header();
?>
<?php if( function_exists('archive_recruit_page' ) ) archive_recruit_page(); ?>

<?php get_footer(); ?>