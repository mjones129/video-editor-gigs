<?php
$absolute_url = mje_get_full_url( $_SERVER );
$title = get_theme_mod('post_job_title') ? get_theme_mod('post_job_title') : __('Get your stuffs done from $5', 'enginethemes');

if( is_mje_submit_page() ){
    $post_link = '#';
}
else {
    $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
}
?>

<?php if( ! get_theme_mod( 'mje_disable_banner') ) : ?>
<div class="banner">
    <div class="container">
        <div class="search-slider float-center job-items-title">
            <h2 class="banner-title"><?php echo $title; ?></h2>
            <a href="<?php echo $post_link; ?>" class="btn-post hvr-sweep-to-left waves-effect waves-light"><p class="name-button-post"><?php _e('Post a mJob', 'enginethemes'); ?></p> <span class="cirlce-plus"><i class="fa fa-plus"></i></span></a>
        </div>
    </div>
    <div class="header-images">
        <?php
        $img_url = ae_get_option('post_job_banner');
        $img_theme_mod = get_theme_mod('post_job_banner');
        if(!empty($img_url)) {
            $img_url = $img_url['full']['0'];
            ?>
            <img src="<?php echo $img_url; ?>" alt="<?php _e('Post a mJob banner', 'enginethemes'); ?>">
            <?php
        } elseif(false === $img_theme_mod) {
            $img_url = get_template_directory_uri() . '/assets/img/banner.png';
            ?>
            <img src="<?php echo $img_url; ?>" alt="<?php _e('Post a mJob banner', 'enginethemes'); ?>">
            <?php
        } else {
            $img_url = "";
        }

        ?>
    </div>
</div>
<?php endif; ?>