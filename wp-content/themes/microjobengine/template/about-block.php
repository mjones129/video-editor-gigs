<?php
$about_title = get_theme_mod('about_title') ? get_theme_mod('about_title') : __('ABOUT MICROJOB ENGINE', 'enginethemes');
$about_link = get_theme_mod('about_link') ? get_theme_mod('about_link') : '#';

?>
<div class="block-intro">
    <div class="container">
        <p class="block-title float-center"><?php echo $about_title; ?></p>
        <ul>
            <?php
                $default_text = array(
                    __('Effortless shopping', 'enginethemes'),
                    __('Be tagged and follow', 'enginethemes'),
                    __('Paid highly', 'enginethemes')
                );
                for($i = 1; $i <= 3; $i++) {
                    $about_col["title_{$i}"] = get_theme_mod("about_col_{$i}_title") ? get_theme_mod("about_col_{$i}_title") : $default_text[$i-1];
                    $about_col["desc_{$i}"] = get_theme_mod("about_col_{$i}_desc") ? get_theme_mod("about_col_{$i}_desc") : 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?';
                    $about_col["link_{$i}"] = get_theme_mod("about_col_{$i}_link") ? get_theme_mod("about_col_{$i}_link") : '#';
                    $about_col_icon = wp_get_attachment_image_src(get_theme_mod("about_col_{$i}_icon"));
                    $about_col["icon_{$i}"] = isset($about_col_icon[0]) ? $about_col_icon[0] : get_template_directory_uri() . "/assets/img/icon-intro-{$i}.png";
                    ?>
                    <li class="col-lg-4 col-md-4 col-sm-12 col-xs-12 clearfix wow fadeInUp <?php echo "block-intro-" . $i; ?>">
                        <div class="icon-article pull-left">
                            <img src="<?php echo $about_col["icon_{$i}"]; ?>" alt="">
                        </div>
                        <div class="text-article pull-right">
                            <h5><a href="<?php echo $about_col["link_{$i}"]; ?>" class="title"><?php echo $about_col["title_{$i}"]; ?></a></h5>
                            <p><?php echo $about_col["desc_{$i}"]; ?></p>
                        </div>
                    </li>
                    <?php
                }
            ?>
        </ul>
        <div class="load-more float-center">
            <a href="<?php echo $about_link; ?>" class="hvr-wobble-vertical"><?php _e('FIND OUT MORE', 'enginethemes'); ?><i class="fa fa-angle-right"></i></a>
        </div>
    </div>
</div>
