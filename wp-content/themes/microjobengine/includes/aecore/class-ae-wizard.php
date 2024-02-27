<?php
/**
 * AE overview
 * show all post, payment, order status on site
 * @package AE
 * @version 1.0
 * @author Dakachi
 */
class AE_Wizard extends AE_Page
{

    public function __construct() {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'et-wizard') {
            $this->add_action('admin_enqueue_scripts', 'wizard_scripts');
        }
        $this->add_action('admin_notices', 'notice_after_installing_theme');
        $this->add_ajax('et-insert-sample-data', 'insert_sample_data' , true , false);
        $this->add_ajax('et-delete-sample-data', 'delete_sample_data',true,false);
    }
    // show url to wizard after active theme
    public function notice_after_installing_theme(){
        $wizard_status = apply_filters('ae_disable_notice_wizard', get_option('option_sample_data', 0));
        if ( isset($wizard_status ) && !$wizard_status ){
        ?>
            <style type="text/css">
            .et-updated{
                background-color: lightYellow;
                border: 1px solid #E6DB55;
                border-radius: 3px;
                webkit-border-radius: 3px;
                moz-border-radius: 3px;
                margin: 20px 15px 0 0;
                padding: 0 10px;
            }
            </style>
            <div id="notice_wizard" class="et-updated">
                <p>
                    <?php
                    $msg = apply_filters( 'notice_after_installing_theme', sprintf( __("You have just installed DirectoryEngine theme, we recommend you follow through our <a href='%s'>setup wizard</a> to set up the basic configuration for your website! <a href='%s'>Close this message</a>", 'enginethemes'), admin_url('admin.php?page=et-wizard'), add_query_arg('close_notices', '1')));
                    echo $msg;
                    ?>
                </p>
            </div>
        <?php
        }
    }

	// install demo menu after done
	function intall_demo_menus(){
		$menu_name = 'Mje Header Menu';
		$theme_location ='et_header_standard';
		$mje_taxonomy='mjob_category';
		$menu_exists = wp_get_nav_menu_object( $menu_name );

		// If it doesn't exist, let's create it.
		if( !$menu_exists){
			$menu_id = wp_create_nav_menu($menu_name);

			$locations = get_theme_mod('nav_menu_locations');
			$locations[$theme_location] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );

			// create item for menu demo
			$terms = get_terms( array(
				'taxonomy' => $mje_taxonomy,
				'hide_empty' => false,
				'parent' => 0,
			) );
			foreach($terms as $term){
				$args=array(
					'menu-item-title' => $term->name,
					'menu-item-url' => get_term_link($term),
					'menu-item-object-id' => $term->term_id,
					'menu-item-object' =>$mje_taxonomy,
					'menu-item-parent-id' => 0,
					'menu-item-type' => 'taxonomy',
					'menu-item-status' => 'publish',
				);

				$childrens=get_terms( array(
						'taxonomy' => $mje_taxonomy,
						'hide_empty' => false,
						'parent' => $term->term_id,
				) );
				$parent_id=wp_update_nav_menu_item($menu_id, 0,$args);
				foreach($childrens as $child){
					$args=array(
						'menu-item-title' => $child->name,
						'menu-item-url' => get_term_link($child),
						'menu-item-object-id' => $child->term_id,
						'menu-item-object' =>$mje_taxonomy,
						'menu-item-parent-id' => $parent_id,
						'menu-item-type' => 'taxonomy',
						'menu-item-status' => 'publish',
					);
					$child_id = wp_update_nav_menu_item($menu_id, 0,$args);
				}//end for child

			}//end for parent

		} else {
            // exist menu but did not assign to locaiton
            $locations = get_nav_menu_locations();
            //get_theme_mod( 'nav_menu_locations' );

            if(empty($locations['et_header_standard'])){

                $header_menu = wp_get_nav_menu_object('header');


                if(empty($header_menu) || !$header_menu){
                    // since 1.3.9
                    $header_menu = wp_get_nav_menu_object('Mje Header Menu'); // prior 1.3.9 sample
                }
                // $menu_name = 'Mje Header Menu';
                // wp_delete_nav_menu($menu_name);

                if(!empty($header_menu) && !is_wp_error( $header_menu )){

                    $menu_id = $header_menu->term_id;
                    $locations['et_header_standard'] = $menu_id;
                    set_theme_mod('nav_menu_locations',$locations );
                }
            }
        }
	}

    public function insert_sample_data(){

        $response = array('success' => false, 'updated_op' => get_option('option_sample_data'));

        if ( !$response['updated_op'] ) {
            update_option( 'option_sample_data', 1);

            $import_xml = new DE_Import_XML();
            $import_xml->dispatch();
            //do_action( 'de_setup_default_theme' );

            $response = array(
                'success'    => true,
                'redirect'   => admin_url('admin.php?page=revslider'),
                'msg'        => __("Import Data Successfully.",'enginethemes'),
                'updated_op' => true
            );

            do_action('ae_insert_sample_data_success');
			$this->intall_demo_menus();
			update_option('show_on_front','posts');
        }

        wp_send_json($response);
    }

    public function delete_sample_data(){

        $response = array('success' => false, 'updated_op' => get_option('option_sample_data'));
        if ( $response['updated_op'] ) {
            delete_option( 'option_sample_data');
			$menu_name = 'Mje Header Menu';
			wp_delete_nav_menu($menu_name);

            $import_xml = new DE_Import_XML();
            $import_xml->depatch();
            $response = array(
                'success'    => true,
                'msg'        => __("Delete Data Successfully.",'enginethemes'),
                'updated_op' => false
            );




        }
        wp_send_json($response);

    }
    function wizard_scripts(){
        $this->add_script('ae-wizard', ae_get_url() . '/assets/js/wizard.js', array(
                    'jquery',
                    'appengine'
                ));
        wp_localize_script(
            'ae-wizard',
            'ae_wizard',
            array(
                'insert_sample_data' => __("Insert sample data", 'enginethemes'),
                'delete_sample_data' => __("Delete sample data", 'enginethemes'),
                'insert_fail'        => __('Insert sample data false','enginethemes'),
                'delete_fail'        => __('Delete sample data false','enginethemes'),
                'wr_uploading'       => __("It would take up to a few minutes for your images to be uploaded to the server. Please don't close or reload this page.")
                )
            );
    }
    /**
     * render container element
     */
    function render() {
        ob_start();
    ?>
        <div class="et-main-content" id="overview_settings">
            <div class="et-main-right">
                <div class="et-main-main clearfix inner-content" id="wizard-sample">

                    <div class="title font-quicksand" style="padding-top:0;">
                        <h3><?php _e('SAMPLE DATA','enginethemes') ?></h3>

                        <div class="desc small"><?php _e('The sample data include some items from the list below: places, comments, etc.','enginethemes') ?></div>

                        <div class="btn-language padding-top10 f-left-all" style="padding-bottom:15px;height:65px;margin:0;">
                        <?php
                            $sample_data_op = get_option('option_sample_data');
                            if (!$sample_data_op) {
                                echo '<button class="primary-button" id="install_sample_data">'.__("Install sample data", 'enginethemes').'</button>';
                            }
                            else{
                                echo '<button class="primary-button" id="delete_sample_data">'.__("Delete sample data", 'enginethemes').'</button>';
                            }
                        ?>
                        </div>
                    </div>

                    <div class="desc" style="padding-top:0px;">
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=place' ); ?>" ><?php _e('Places','enginethemes') ?></a> <span class="description"><?php _e('Add new place types or modify the sample ones to suit your site style.','enginethemes') ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit-tags.php?taxonomy=place_category&post_type=place' ); ?>" ><?php _e('Place Categories','enginethemes') ?></a> <span class="description"><?php _e('Add new categories or modify the sample data to match your directory business.','enginethemes') ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit-tags.php?taxonomy=location&post_type=place' ); ?>" ><?php _e('Place Locations','enginethemes') ?></a> <span class="description"><?php _e('Add new locations or modify the sample data to match your directory business.','enginethemes') ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>" ><?php _e('Pages','enginethemes') ?></a> <span class="description"><?php _e('Modify the sample "About us, Contact us, ..." pages or add your extra pages when needed.','enginethemes') ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php' ); ?>" ><?php _e('Posts','enginethemes') ?></a> <span class="description"><?php _e('A couple of news & event posts have been added for your review. You can delete it or add your own posts here.','enginethemes') ?></span>
                        </div>
                    </div>
                </div>

                <div class="et-main-main clearfix inner-content <?php if (!$sample_data_op) echo 'hide'; ?>" id="overview-listplaces">

                    <div class="title font-quicksand" style="padding-bottom:60px;">
                        <h3><?php _e('MORE SETTINGS','enginethemes') ?></h3>
                        <div class="desc small"><?php _e('Enhance your site by customizing these other features','enginethemes') ?></div>
                    </div>

                    <div style="clear:both;"></div>

                    <div class="title font-quicksand  sample-title">
                        <a target="_blank"  href="admin.php?page=et-settings" ><?php _e('General Settings','enginethemes') ?></a> <span class="description"><?php _e('Modify your site information, social links, analytics script, or add a language, etc.','enginethemes') ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank"  href="edit.php?post_type=page" ><?php _e('Front Page','enginethemes') ?></a> <span class="description"><?php _e('Rearrange content elements or add more information in your front page to suit your needs.','enginethemes') ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank" href="nav-menus.php" ><?php _e('Menus','enginethemes') ?></a> <span class="description"><?php _e('Edit all available menus in your site here.','enginethemes') ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a href="widgets.php" target="_blank"><?php _e('Sidebars & Widgets','enginethemes') ?></a> <span class="description"><?php _e('Add or remove widgets in sidebars throughout the site to best suit your need.','enginethemes') ?></span>
                    </div>

                </div>
            </div>
        </div>
        <style type="text/css">
        .hide{display: none;}.et-main-left .title, .et-main-main .title {text-transform: none;}.et-main-main{margin-left:0;}.title.font-quicksand h3{margin-bottom:0;margin-top:0;}.desc.small,span.description{font-family:Arial, sans-serif!important;font-weight:400;font-size:16px!important;color:#9d9d9d;font-style:normal; margin-top:10px; }span.description{margin-left:30px;}.sample-title{color:#427bab!important;padding-left:20px!important;font-size:18px!important;}.title.font-quicksand{padding-top:15px;}a.primary-button{right:50px;position:absolute;text-decoration:none;color:#ff9b78;}.et-main-main .title{padding-left:20px;}.sample-title a{text-decoration: none;}
        </style>
    <?php
    $html = ob_get_clean();
    $html = apply_filters('ae_setup_wizard_template', $html);
    echo $html;
    }
}

if(class_exists('ET_Import_XML')) {
    class DE_Import_XML extends ET_Import_XML {
        function __construct () {
            $this->file = TEMPLATEPATH . '/sampledata/sample_data.xml';
        }
    }
}