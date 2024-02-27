<h2>
    <span class="rendered-text">
        <?php echo $mjob_post->post_title; ?>
    </span>
    <?php if( MJE_MJob_Action::checkEdit( $mjob_post ) ): ?>
        <a href="#" class="edit-mjob-action">
            <i class="fa fa-pencil"></i>
            <span>
                <?php _e('EDIT', 'enginethemes') ?>
            </span>
        </a>
    <?php endif; ?>
</h2>
<?php do_action('after_mjob_detail_title', $mjob_post);?>
<div class="mjob-single-meta clearfix">
    <span class="mjob-cat-breadcrumb">
        <?php
        $cats = $mjob_post->tax_input['mjob_category'];
        $breadcrumb = '';
        if (!empty($cats)) {
            $parent = $cats['0']->parent;
            $breadcrumb = '<span class="mjob-breadcrumb"><a class="parent" href="' . get_term_link($cats["0"]) . '">' . $cats["0"]->name . '</a></span>';
            if ($parent != 0) {
                $parent = get_term_by('ID', $parent, 'mjob_category');
                if($parent)
                $breadcrumb = '<span class="mjob-breadcrumb"><a class="parent" href="' . get_term_link($parent) . '">' . $parent->name . '</a> <i class="fa fa-angle-right"></i> <span><a class="child" href="' . get_term_link($cats["0"]) . '">' . $cats['0']->name . '</a></span></span>';
            }
        }
        echo $breadcrumb;
        ?>
    </span>
    <span class="time-post pull-right">
        <?php _e('Last modified: ', 'enginethemes'); ?>
        <span><?php echo $mjob_post->modified_date; ?></span>
    </span>
</div>