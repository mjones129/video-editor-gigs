<?php
/**
Plugin Name: MJE Geolocation
Plugin URI: http://enginethemes.com/
Description: A plugin that allows to using GEO Location to filter/search mjob service.
Version: 1.4
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginethemes
*/
//lab_key AIzaSyCTO2Z0jXBsJNZP-6Lorfc-_gnovcsiqtM

define ('MAP_STATIC_KEY','AIzaSyCrcWa0zKdiFzZDdoUBjcdTJ-8GDVoj66o');

define( 'MJE_GEOLOCATION_VER', '1.4' );

define( 'MJE_GEOLOCATION_PATH', dirname( __FILE__ ) );
define( 'MJE_GEOLOCATION_URL', plugin_dir_url( __FILE__ ) );

define( 'MJE_GEOLOCATION_DEBUG', true);

defined( 'ABSPATH' ) || exit;


Class MJE_Geolocation{
	public $api_url;
	public $api_key;
	function __construct(){

		$this->include_files();
		$this->init_hooks();

	}
	function is_ready(){
		$api_key  = ae_get_option('mje_geo_map_api_key', true);
		$this->api_key = trim($api_key);
		if($this->api_key)
			return true;
		return false;
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	function include_files(){
		require_once MJE_GEOLOCATION_PATH . '/template/requires.php';
		include_once MJE_GEOLOCATION_PATH . '/includes/functions.php';
		require_once MJE_GEOLOCATION_PATH . '/debug.php';
		require_once MJE_GEOLOCATION_PATH . '/update.php';
		require_once MJE_GEOLOCATION_PATH . '/wp-query-geo.php';


	}

	function install(){
		// where to upgrade db if have.
	}

	/**
	 * catch all WordPress hook in this method.
	*/
	function init_hooks(){
		register_activation_hook( __FILE__ , array( $this, 'install' ) );
		add_action( 'input_address_field', array($this,'input_html_address_field'));
		add_action( 'wp_enqueue_scripts', array($this, 'geolocation_enqueue_script') );
		add_action( 'edit_input_address_field', array($this,'input_html_address_field')); // post_project form

		add_filter( 'mjob_post_meta_fields', array($this,'add_mje_geo_meta'));
		// add_action( 'mjob_post_after_description', array($this, 'show_mjob_single_map'));

		add_action( 'mje_geo_search_form', array($this, 'mje_geo_search_form_html')); // search from in home page.
		//add_filter( 'mje_mjob_search_in_url', array($this, 'apply_mje_query'));
		add_action( 'pre_input_tag_filter', array($this,'location_tax_filter'), 9);
		add_action( 'pre_input_tag_filter', array($this,'slide_distance_filter'), 11);

		add_action( 'after_setup_theme', array($this, 'mje_include_settings') );

		add_filter('custom_new_tax', array($this,'add_new_location_taxonomy'));
		add_filter('ae_post_taxs',array($this, 'add_init_tax'), 10 ,2);

		add_action('mje_mjob_item_after_image',array($this, 'show_address_in_hover') );
		add_action('mje_mjob_item_js_after_image',array($this, 'show_address_in_hover_js') );


		add_action('after_mjob_detail_title',array($this, 'show_full_address_in_detail') );

		add_filter('mje_mjob_filter_query_args',array($this, 'apply_location_filter') );
	}
	function apply_location_filter($query_args){

		$query = $_REQUEST['query'];

		if(isset($query['location']) && !empty($query['location'])) {
            // Filter by skill only
            $locations = $query['location'];
            $query_args['tax_query'][] =
                array(
                    'taxonomy' => 'location',
                    'field' => 'slug',
                    'terms' => $locations

            );
        }

        return $query_args;
	}
	function show_full_address_in_detail($mjob_post){

		$api_url = "https://www.google.com/maps/search/?api=1&query=";

		if( !empty($mjob_post->et_full_location) || isset($mjob_post->tax_input['location']) ) {
			echo '<div class="fullline">';
			if( $mjob_post->et_full_location){
				$api_url.=$mjob_post->et_full_location;
				echo '<span class="et_full_location"><a target="_blank" rel="'.__('View in google map','mje_geo').'" href="'.esc_url($api_url).'" title="'.__('View in google map','mje_geo').'"><i class="fa fa-map-marker" aria-hidden="true"></i> '.$mjob_post->et_full_location.'</a></span>';
			}
			if(isset($mjob_post->tax_input['location'])){
				$tax = $mjob_post->tax_input['location'];
				if(isset($tax[0])){
					$location = $tax[0];
					echo '<span class="et_tax_location">'.sprintf('Location: %s',$location->name).'</span>';
				}
			}
			echo '</div>';
		}
	}
	function show_address_in_hover($current){
		$acti_hover = ae_get_option('mjegeo_hover', true);
		if($acti_hover){
			if( $current->et_full_location){
				echo '<span class="et_full_location"><i class="fa fa-map-marker" aria-hidden="true"></i> '.$current->et_full_location.'</span>';
			}
		}
	}
	function show_address_in_hover_js($current){
		$acti_hover = ae_get_option('mjegeo_hover', true);
		if($acti_hover){ ?>
			<# if( et_full_location ) { #>
				<span class="et_full_location"><i class="fa fa-map-marker" aria-hidden="true"></i> {{=et_full_location}}</span>
			<# } #>
		<?php
		}
	}
	function add_init_tax($taxs, $post_type){
		if($post_type == 'mjob_post')
			array_push($taxs, 'location');
		return $taxs;

	}
	function get_api_url(){
		if( ! $this->is_ready()){
			return false;
		}

		$lang_code = ae_get_option('mjegeo_lang_code','en');
		return "https://maps.googleapis.com/maps/api/js?key=".$this->api_key."&language=".trim($lang_code);
	}
	function add_new_location_taxonomy($args){
		//array('has_new_custom_tax'=> false,'label' =>'','args' => array() )
		$args['has_new_custom_tax'] = true;


		 $label = array(
            'name' => __('Locations', 'enginethemes') ,
            'singular_name' => __('Locations', 'enginethemes') ,
            'search_items' => __('Search Locations', 'enginethemes') ,
            'popular_items' => __('Popular Locations', 'enginethemes') ,
            'all_items' => __('All Locations', 'enginethemes') ,
            'parent_item' => __('Parent Location', 'enginethemes') ,
            'parent_item_colon' => __('Parent Locations', 'enginethemes') ,
            'edit_item' => __('Edit Location', 'enginethemes') ,
            'update_item' => __('Update Location', 'enginethemes') ,
            'add_new_item' => __('Add New Locations', 'enginethemes') ,
            'new_item_name' => __('New Location Name', 'enginethemes'),
            'add_or_remove_items' => __('Add or remove Tags', 'enginethemes'),
            'choose_from_most_used' => __('Choose from most used enginetheme', 'enginethemes') ,
            'menu_name' => __('Locations', 'enginethemes') ,
        );

		 $args_cs = array(
            'hierarchical'=> true,
        );
		$args['label'] = $label;
		$args['args'] = $args_cs;
		$args['tax'] = 'location';
		return $args;

	}

	function mje_include_settings(){
		global $lang_code;
		require_once MJE_GEOLOCATION_PATH . '/language.php';
		require_once MJE_GEOLOCATION_PATH . '/setting.php';
	}

	function location_tax_filter(){ ?>
		<div class="filter-tags">
			<p  class="title-menu"><?php _e('Locations', 'enginethemes'); ?></p>
			<?php
				$location = array();
                if( !empty($_GET['location'])) {
                    	$location = explode(',', $_GET['location'] );
                }
				//mje_show_filter_tags(array('skill'), array('hide_empty' => false));
				echo '<div class="tags et-form">';
				ae_tax_dropdown( 'location' , array(
                        'attr' => 'multiple data-placeholder="'.__("Filter by location", 'enginethemes').'"',
                        'class' => 'multi-tax-item is-chosen',
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'value' => 'slug',
                        'hide_empty' => false,
                        'hierarchical' => true ,
                        'selected' => $location,
                        'show_option_all' => false,
                ));
                echo "</div>";
			?>
		</div>

	<?php }
	function slide_distance_filter(){
		return false; //temporary hidden this in beta vesion.
		if( ! $this->is_ready()){
			return false;
		}

		$latitude 	= isset($_GET['latitude']) ? $_GET['latitude'] : 0;
		$longitude 	= isset($_GET['longitude']) ? $_GET['longitude'] : 0;
		$value = "[0,200]"; // 2 slider
		$value = 0;// 1 slider;
		$distance = ae_get_option('mjegeo_distance', 1000);
		$unit_key = (int) ae_get_option('mjegeo_unit');// 0 ==KM, 1 == MILES
		$unit = 'MILES';
		if( $unit_key == 1) {
			$unit = 'KM';
		}

		$mjegeo_debug = ae_get_option('mjegeo_debug', 0);


		?>
		<div class="filter-tags fillter-distance">
			<p  class="title-menu">
				<?php _e('Distance Filter', 'enginethemes'); ?></p>


			<span class="cur-distance"><?php echo $distance;?></span> <?php echo $unit;?></span></p>
            <input type="text" class="slider-value-default slider-ranger"
                                    data-slider-min="0" data-slider-max="<?php echo $distance;?>"
                                   	data-slider-step="10"
                                    data-slider-value="<?php echo esc_attr($value);?>"
                                    data-slider-orientation="horizontal" data-slider-selection="before"
                                    data-slider-tooltip="show" name="distance"
                                />
            <input type="hidden" name="latitude" id="latitude" value="<?php echo esc_attr( $latitude ); ?>">
            <input type="hidden" name="longitude" id="longitude" value="<?php echo esc_attr( $longitude ); ?>">
            <input type="hidden" name="place_tax" id="place_tax">


		</div>
		<script src="<?php echo $this->get_api_url();?>" async defer></script>
		<?php

	}

	function mje_geo_search_form_html(){
		if( ! $this->is_ready() ){
			return;
		}
		mjegeo_search_home($this->api_key);
	}
	function input_html_address_field(){

		if( ! $this->is_ready() ){
			return;
		}

		mjegeo_post_service_input($this->get_api_url());
	}
	function show_mjob_single_map($mjob_post){
		if( ! $this->is_ready() ){
			return;
		}

		$terms = get_the_terms( $mjob_post->ID, 'location' );

		if ( $terms && ! is_wp_error( $terms ) ) :

		    $draught_links = array();

		    foreach ( $terms as $term ) {
		        $draught_links[] = $term->name;
		    }

		    $on_draught = join( ", ", $draught_links );
		    ?>

		    <p class="location draught">
		        <?php echo esc_html( $on_draught ) ; ?>
		    </p>
		<?php endif; ?>
		<?php

		if($mjob_post->et_full_location){ ?>

			<?php echo $mjob_post->et_full_location;?>

			<div id="singlemap"></div>
	      	<script>
		      	var singlemap;

		      	function initSingleMap() {
		        	singlemap = new google.maps.Map(document.getElementById('singlemap'), {
		          	center: {lat: <?php echo $mjob_post->et_location_lat;?>, lng: <?php echo $mjob_post->et_location_lng;?>},
		          	zoom: 13
		        });
		        var pos = {
	              	lat: <?php echo $mjob_post->et_location_lat;?>,
	              	lng: <?php echo $mjob_post->et_location_lng;?>
	            };

		        var marker = new google.maps.Marker({
		            //map: window.map,
		            draggable:false,
		            //animation: google.maps.Animation.DROP,
		            position: pos,
		            title:'auto geo',
		        });
		        marker.setMap(null);
		        marker.setMap(singlemap);

		    }
		    </script>
		    <script src="<?php echo $this->get_api_url();?>&callback=initSingleMap" async defer></script>
			<?php
		}

	}
	function add_mje_geo_meta($metas){
		array_push($metas,'et_full_location', 'et_location_lat', 'et_location_lng');
		return $metas;
	}
	public function geolocation_enqueue_script(){
		if( ! $this->is_ready()){
			return;
		}
		wp_enqueue_script(
			'geo-distance-slider-bar',
			MJE_GEOLOCATION_URL. 'assets/slider-bt.js',
			array(
				'jquery',
				'underscore',
				'backbone',
				'appengine'
			),
			MJE_GEOLOCATION_VER ,
			true
		);
		wp_enqueue_script(
			'mje-geolocation',
			MJE_GEOLOCATION_URL. 'assets/mje_geolocation.js',
			array(
				'jquery',
				'underscore',
				'backbone',
				'appengine'
			),
			MJE_GEOLOCATION_VER ,
			true
		);

		wp_enqueue_style(
			'slider-range',
	        MJE_GEOLOCATION_URL. 'assets/slider.css',
	       	'',
	        MJE_GEOLOCATION_VER
	    );



		if( is_singular('mjob_post')){
			global $ae_post_factory, $post;
			$mjob_object = $ae_post_factory->get( 'mjob_post' );
			$mjob_post = $mjob_object->convert( $post );

			$curPos =  array(
				'et_full_location' => $mjob_post->et_full_location,
				'pos' => array(
					'lat' => $mjob_post->et_location_lat,
					'long' => $mjob_post->et_location_lng
				)
			);
			wp_localize_script( 'mje-geolocation', 'curPos', $curPos );
		}
		if( is_search() ){
			$curPos =  array(
				'lat' => isset($_GET['latitude']) ? $_GET['latitude'] : 0,
				'long' => isset($_GET['longitude']) ? $_GET['longitude']: 0,
			);
			wp_localize_script( 'mje-geolocation', 'curPos', $curPos );
		}

		wp_enqueue_style(
			'mje-geo-style',
	        MJE_GEOLOCATION_URL. 'assets/geo-style.css',
	       	'',
	        MJE_GEOLOCATION_VER
	    );
	}

}


$GLOBALS['MjE_GEO'] = new MJE_Geolocation();