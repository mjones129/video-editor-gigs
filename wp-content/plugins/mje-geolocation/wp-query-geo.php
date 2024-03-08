<?php if(!defined('ABSPATH')) { die(); } // Include in all php files, to prevent direct execution

//https://gschoppe.com/wordpress/location-searches/
define ('ET_LAT_META','et_location_lat');
define ('ET_LONG_META','et_location_lng');

function is_mje_geo_admin_request() {
	if ( function_exists( 'wp_doing_ajax' ) ) {
		return is_admin() &&   ! wp_doing_ajax();
	} else {
		return is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

}

if( ! class_exists('MjeGeoQuery') ) {
	class MjeGeoQuery {
		public static function Instance() {
			static $instance = null;
			if ($instance === null) {
				$instance = new self();
			}
			return $instance;
		}

		private function __construct() {

			add_filter( 'posts_fields' , array( $this, 'posts_fields'  ), 10, 2 );
			add_filter( 'posts_join'   , array( $this, 'posts_join'    ), 10, 2 );
			add_filter( 'posts_where'  , array( $this, 'posts_where'   ), 10, 2 );
			add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
		}

		// add a calculated "distance" parameter to the sql query, using a haversine formula
		public function posts_fields( $sql, $query ) {
			$t = is_mje_geo_admin_request();

			if( is_mje_geo_admin_request() )
				return $sql;

			// if ( ! $query->is_search() )
			// 	return $sql;

			global $wpdb;


			$geo_query = $this->is_geo_query($query);

			if( $geo_query ) {

				if( $sql ) {
					$sql .= ', ';
				}
				$sql .= $this->haversine_term( $geo_query ) . " AS geo_query_distance";
			}

			return $sql;
		}
		function is_geo_query($query){

			$lat 	= isset($_GET['latitude'])  ? $_GET['latitude'] : '';
			$long 	= isset($_GET['longitude'])  ? $_GET['longitude'] : '';
			if( !empty($lat) && !empty($long) ){
				return true;
			}
			if( isset($_POST['query'])){
				$query = $_POST['query'];

				$lat 	= isset($query['latitude'])  ? $query['latitude'] : '';
				$long 	= isset($query['longitude'])  ? $query['longitude'] : '';
				if( !empty($lat) && !empty($long) ){
					return true;
				}

			}
			return false;
		}

		public function posts_join( $sql, $query ) {
			if( is_mje_geo_admin_request() )
				return $sql;

			// if ( ! $query->is_search() )
			// 	return $sql;

			global $wpdb;
			$geo_query = $this->is_geo_query($query);

			if( $geo_query ) {

				if( $sql ) {
					$sql .= ' ';
				}
				$sql .= "INNER JOIN " . $wpdb->prefix . "postmeta AS geo_query_lat ON ( " . $wpdb->prefix . "posts.ID = geo_query_lat.post_id ) ";
				$sql .= "INNER JOIN " . $wpdb->prefix . "postmeta AS geo_query_lng ON ( " . $wpdb->prefix . "posts.ID = geo_query_lng.post_id ) ";
			}

			return $sql;
		}

		// match on the right metafields, and filter by distance
		public function posts_where( $sql, $query ) {
			if( is_mje_geo_admin_request() )
				return $sql;
			// if ( 'mjob_post' !== $query->get( 'post_type' ) )
			// 	return $sql;
			// if ( ! $query->is_search() ){
			// 	var_dump('none search');
			// 	return $sql;
			// }
			global $wpdb;
			$geo_query = $this->is_geo_query($query);

			if( $geo_query ) {

				$lat_field = ET_LAT_META;
				if( !empty( $geo_query['lat_field'] ) ) {
					$lat_field =  $geo_query['lat_field'];
				}
				$lng_field = ET_LONG_META;
				if( !empty( $geo_query['lng_field'] ) ) {
					$lng_field =  $geo_query['lng_field'];
				}
				$df_distance = ae_get_option('mjegeo_distance', 1000);
				$query = isset($_POST['query']) ? $_POST['query']: array();
				$distance = isset($query['distance']) ? $query['distance'] : $df_distance;

				if( $sql ) {
					$sql .= " AND ";
				}
				$haversine = $this->haversine_term( $geo_query );
				$new_sql = "( geo_query_lat.meta_key = %s AND geo_query_lng.meta_key = %s AND " . $haversine . " <= %f )";
				//$new_sql = "( geo_query_lat.meta_key = %s AND geo_query_lng.meta_key = %s AND geo_query_distance <= %f )";

				$sql .= $wpdb->prepare( $new_sql, $lat_field, $lng_field, $distance );

			}

			return $sql;
		}

		// handle ordering
		public function posts_orderby( $sql, $query ) {
			if( is_mje_geo_admin_request() ){
				return $sql;
			}
			// if ( 'mjob_post' !== $query->get( 'post_type' ) )
			// 	return $sql;

			$geo_query = $this->is_geo_query($query);

			if( $geo_query ) {
				$orderby = $query->get('orderby');
				$order   = $query->get('order');
				if( $orderby == 'distance' ) {
					if( !$order ) {
						$order = 'ASC';
					}
					$sql = 'geo_query_distance ' . $order;
				}
			}

			return $sql;
		}

		public static function the_distance( $post_obj = null, $round = false ) {
			echo self::get_the_distance( $post_obj, $round );
		}

		public static function get_the_distance( $post_obj = null, $round = false ) {
			global $post;
			if( !$post_obj ) {
				$post_obj = $post;
			}
			if( property_exists( $post_obj, 'geo_query_distance' ) ) {
				$distance = $post_obj->geo_query_distance;
				if( $round !== false ) {
					$distance = round( $distance, $round );
				}
				return $distance;
			}
			return false;
		}

		private function haversine_term( $geo_query ) {
			global $wpdb;
			//$units = "miles";
			$units = ae_get_option('mjegeo_unit', 'MILES');
			// if( !empty( $geo_query['units'] ) ) {
			// 	$units = strtolower( $geo_query['units'] );
			// }
			$radius = 3959; // match with miles unit
			if( $units == 'KM' ) {
				$radius = 6371; // match with KM
			}
			$lat_field = "geo_query_lat.meta_value";
			$lng_field = "geo_query_lng.meta_value";
			$lat = 0;
			$long = 0;
			if( isset( $_GET['latitude'] ) ) { // update
				$lat = $_GET['latitude' ];
			}
			if(  isset( $_GET['longitude'] ) ) {// update
				$long = $_GET['longitude'];
			}

			if( is_admin() &&  ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
				if( isset($_POST['query'])){
					$query = $_POST['query'];
					$lat 	= isset($query['latitude'])  ? $query['latitude'] : '';
					$long 	= isset($query['longitude'])  ? $query['longitude'] : '';
				}
			}
			$haversine  = "( " . $radius . " * ";
			$haversine .=     "acos( cos( radians(%f) ) * cos( radians( " . $lat_field . " ) ) * ";
			$haversine .=     "cos( radians( " . $lng_field . " ) - radians(%f) ) + ";
			$haversine .=     "sin( radians(%f) ) * sin( radians( " . $lat_field . " ) ) ) ";
			$haversine .= ")";
			$haversine  = $wpdb->prepare( $haversine, array( $lat, $long, $lat ) );
			return $haversine;
		}
	}

}
MjeGeoQuery::Instance();

if( !function_exists( 'the_distance' ) ) {
	function the_distance( $post_obj = null, $round = false ) {
		MjeGeoQuery::the_distance( $post_obj, $round );
	}
}

if( !function_exists( 'get_the_distance' ) ) {
	function get_the_distance( $post_obj = null, $round = false ) {
		return MjeGeoQuery::get_the_distance( $post_obj, $round );
	}
}