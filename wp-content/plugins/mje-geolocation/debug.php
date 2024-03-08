<?php
function check_meta_fields_of_mjob_post(){

	if( is_singular('mjob_post') ){

		global $wpdb, $post;
		$sql = "SELECT m.* FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id WHERE p.ID = {$post->ID} 
		AND m.meta_key in ('et_full_location','et_location_lat','et_location_lng')";
		//echo $sql;
		$metas = $wpdb->get_results($sql);
		echo '<pre>';
		//$check = array('et_full_location', 'et_location_lat','et_location_lng');
		if($metas){
			foreach ($metas as $key => $meta) {
				//if( in_array($meta->meta_key,$check) ){
					echo $meta->meta_key.': &nbsp; '.$meta->meta_value .'<br />';
				//}
			}
		} else {
			echo 'Geo Meta is empty';
		}


		global $ae_post_factory, $post;
		$mjob_object = $ae_post_factory->get( 'mjob_post' );
		$mjob_post = $mjob_object->convert( $post );
		//var_dump($mjob_post);
		echo '</pre>';
	}
	echo $GLOBALS['wp_query']->request;
}
//add_action('wp_footer','check_meta_fields_of_mjob_post');
?>