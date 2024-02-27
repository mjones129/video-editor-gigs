<?php do_action('pre_input_tag_filter');?>
<div class="filter-tags">
	<p  class="title-menu"><?php _e('Tags', 'enginethemes'); ?></p>
	<?php
		$skill = array();
        if( !empty($_GET['skill_ids'])) {
            	$skill = explode(',', $_GET['skill_ids'] );
        }
		//mje_show_filter_tags(array('skill'), array('hide_empty' => false));
		echo '<div class="tags et-form">';
		ae_tax_dropdown( 'skill' , array(
                'attr' => 'multiple data-placeholder="'.__("Filter by tag", 'enginethemes').'"',
                'class' => 'multi-tax-item is-chosen',
                'hide_empty' => false,
                'hierarchical' => true ,
                'selected' => $skill,
                'show_option_all' => false,
        ));
        echo "</div>";
	?>
</div>
<div class="advanced-filters et-form">
	<p  class="title-menu"><?php _e('FILTER', 'enginethemes'); ?></p>

	<?php do_action( 'mje_template_search_advance_before' ); ?>

	<div class="tags  filter-language advanced-filters-item">
		<p class="filter-title"><?php _e('Language', 'enginethemes'); ?></p>
        <div class="choose-language">
            <?php
            $language_ids = array();
            if( !empty($_GET['language_ids'])) {
            	$language_ids = explode(',', $_GET['language_ids'] );
            }
            ae_tax_dropdown( 'language' , array(
                'attr' => 'multiple data-placeholder="'.__("Choose the language(s)", 'enginethemes').'"',
                'class' => 'multi-tax-item is-chosen',
                'hide_empty' => false,
                'hierarchical' => true ,
                'id' => 'language' ,
                'selected' => $language_ids,
                'show_option_all' => false,
            ));
            ?>
        </div>
	</div>
	<?php
	 $min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
	 $max = isset($_GET['price_max']) ? $_GET['price_max'] : '';
	?>
	<div class="filter-price-mjob advanced-filters-item">
		<p class="filter-title"><?php _e('Price', 'enginethemes'); ?></p>
        <input  class="filter-budget-min" type="text" name="min_budget" value="<?php echo $min ?>" >
        <span>-</span>
        <input class="filter-budget-max" type="text" name="max_budget" value="<?php echo $max ?>" >
		<button type="button" class="btn filter-price"><?php _e('GO', 'enginethemes'); ?></button>
	</div>
	<?php do_action( 'mje_template_search_advance_after' ); ?>
</div><!-- end .advanced-filters -->
