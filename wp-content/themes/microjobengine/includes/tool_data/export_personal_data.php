<?php
// ajax action: wp-privacy-export-personal-data  - wp_ajax_wp_privacy_export_personal_data
// child filter hook: wp_privacy_personal_data_exporters
// hook data: wp_user_personal_data_exporter
// request data:
/*
post_name: export_personal_data
post_type: user_request
Create a request: wp_create_user_request
*/
//wp_user_personal_data_exporter();
class Fre_Tool_Data{
	public static $instance;
	function __construct(){

	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new Fre_Tool_Data();
		}

		return self::$instance;
	}


}

function register_fre_theme_exporter( $exporters ) {
 	$exporters[] = array(
		'exporter_friendly_name' =>  'MicrojobEngine Data',
		'callback'               => 'fre_personal_data_exporter',
	);
 	$exporters[] = array(
		'exporter_friendly_name' =>  'Your Mjob Posted',
		'callback'               => 'mje_mjob_posted_exporter',
	);
	$exporters[] = array(
		'exporter_friendly_name' =>  'Your Order',
		'callback'               => 'mje_mjob_order_exporter',
	);
	$exporters[] = array(
		'exporter_friendly_name' =>  'Your Recruits',
		'callback'               => 'mje_recruit_exporter',
	);


	return $exporters;

}
add_filter('wp_privacy_personal_data_exporters','register_fre_theme_exporter');

function mje_recruit_exporter($email_address, $page = 1){

	$user = get_user_by( 'email', $email_address );
	$number = 300; $found_posts = 0;
	$project_to_export = $data_to_export = array();

		$args = array(
			'post_type' => 'recruit',
			'author' => $user->ID,
			'posts_per_page' => $number,
			'post_status' => 'any',
		);
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			$found_posts = $the_query->found_posts;
			while ( $the_query->have_posts() ) {
				$project_data_to_export = array();
				$the_query->the_post();

				global $post;
				global $ae_post_factory, $post;
				$mjob_object = $ae_post_factory->get( 'mjob_post' );
				$mjob_post = $mjob_object->convert( $post );

				$metas = list_mjob_post_meta();

				$project_data_to_export[] = array(
					'name'  => 'Recruit ID',
					'value' => $mjob_post->ID,
				);

				$project_data_to_export[] = array(
					'name'  => 'Recruit Name',
					'value' => '<a href="'.get_permalink().'">'.get_the_title().'</a>',
				);
				$project_data_to_export[] = array(
					'name'  => 'Post Date',
					'value' => get_the_date(),
				);
				foreach ($metas as $key => $label) {
					if( ! empty($mjob_post->$key) ){
						$project_data_to_export[] = array(
							'name'  => $label,
							'value' => $mjob_post->$key,
						);
					}
				}

				$item_id = "recruit-{$post->ID}";
				$data_to_export[] = array(
					'group_id'    => 'recruits',
					'group_label' => __( 'List Recruits' ),
					'item_id'     => $item_id,
					'data'        => $project_data_to_export,
				);
			}
			wp_reset_postdata();
		}
		$done = $found_posts < $number;

	//}
	return array(
		'data' => $data_to_export,
		'done' => $done,
	);

}
function list_mjob_order_meta(){
	return array(
           // 'amount' => 'Amount',
            'real_amount' => 'Real Amount',

          //  'mjob_price_text' => 'Price',

            //'paid' => 'Paid',
            'seller_id' => 'Seller id',
            'extra_info' => 'Extra info',
            'fee_commission' => 'Fee commission',
            //'et_order_currency' => 'Currency',
            'et_invoice_no' => 'Invoice No',
        );
}
function mje_mjob_order_exporter($email_address, $page = 1){

	$number = 300; $found_posts= 0;
	$bid_to_export = $data_to_export = array();
	$user = get_user_by( 'email', $email_address );

	$role = ae_user_role($user->ID);

	$args = array(
		'post_type' => 'mjob_order',
		'author' => $user->ID,
		'posts_per_page' => $number,
		'post_status' => 'any',
	);
	$the_query = new WP_Query( $args );

	global $wp_query, $ae_post_factory, $post;
	$mjob_order_obj = $ae_post_factory->get( 'mjob_order' );
	// The Loop
	if ( $the_query->have_posts() ) {

		$found_posts = $the_query->found_posts ;
		while ( $the_query->have_posts() ) {

			$mjob_order_to_export = array();
			$the_query->the_post();
			global $post;
			$convert = $mjob_order_obj->convert($post);


			$mjob_order_to_export[] = array(
				'name'  => 'Mjob Order ID',
				'value' => $convert->ID
			);
			$mjob_order_to_export[] = array(
				'name'  => 'Name',
				'value' => '<a href="'.get_permalink($convert->ID).'">'.$convert->post_title.'</a>'
			);


			$metas = list_mjob_order_meta();
			foreach ($metas as $key => $label) {
				if( ! empty($convert->$key) ){
					$mjob_order_to_export[] = array(
						'name'  => $label,
						'value' => $convert->$key,
					);
				}
			}
			$mjob_order_to_export[] = array(
				'name'  => 'Order Time Delivery',
				'value' => $convert->mjob_time_delivery
			);
			$mjob_order_to_export[] = array(
				'name'  => 'Order Price',
				'value' => $convert->mjob_price_text
			);
			$mjob_order_to_export[] = array(
				'name'  => 'Total',
				'value' => mje_shorten_price($convert->amount)
			);

			$mjob_order_to_export[] = array(
				'name'  => 'Order Date',
				'value' => get_the_date()
			);

			$mjob_order_to_export[] = array(
				'name'  => 'Order Content',
				'value' => $convert->post_content
			);
			$mjob_order_delivery  = get_post_meta($convert->ID, 'mjob_order_delivery', true);
			if( ! $mjob_order_delivery){
                $mjob_order_delivery = $convert->mjob_time_delivery;
            }
            $mjob_order_to_export[] = array(
				'name'  => 'Time Delivery',
				'value' => sprintf(__('%s day(s)', 'enginethemes'), $mjob_order_delivery)
			);

			$item_id = "mjob-order-{$convert->ID}";

			$data_to_export[] = array(
				'group_id'    => 'mjob_orders',
				'group_label' => __( 'List Mjob Orders' ),
				'item_id'     => $item_id,
				'data'        => $mjob_order_to_export,
			);
		} // end while;


		wp_reset_postdata();
	}

	$done = $found_posts < $number;

	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}
function list_mjob_post_meta(){
	return array(
		'time_delivery' => 'Time Delivery',
        'et_price' => 'Price',
        'et_budget_text' => 'Budget',
        'rating_score' => 'Rating Score',
        'et_carousels' => 'Carousol',
        'modified_date' => 'Modified Date',
        'et_total_sales' => 'Total Sale',
        'view_count' => 'Count View',
    );
}
function mje_mjob_posted_exporter($email_address, $page = 1){

	$user = get_user_by( 'email', $email_address );
	$number = 300; $found_posts = 0;
	$project_to_export = $data_to_export = array();
	$item_id = "project-of-{$user->ID}";

	//if( $role == EMPLOYER || $role == 'administrator' ) {
		$args = array(
			'post_type' => 'mjob_post',
			'author' => $user->ID,
			'posts_per_page' => $number,
			'post_status' => 'any',
		);
		$the_query = new WP_Query( $args );


		// The Loop
		if ( $the_query->have_posts() ) {
			$found_posts = $the_query->found_posts;
			while ( $the_query->have_posts() ) {
				$project_data_to_export = array();
				$the_query->the_post();

				global $post;
				global $ae_post_factory, $post;
				$mjob_object = $ae_post_factory->get( 'mjob_post' );
				$mjob_post = $mjob_object->convert( $post );

				$metas = list_mjob_post_meta();

				$project_data_to_export[] = array(
					'name'  => 'Mjob ID',
					'value' => $mjob_post->ID,
				);

				$project_data_to_export[] = array(
					'name'  => 'Mjob Name',
					'value' => '<a href="'.get_permalink().'">'.get_the_title().'</a>',
				);
				$project_data_to_export[] = array(
					'name'  => 'Posted Date',
					'value' => get_the_date(),
				);
				foreach ($metas as $key => $label) {
					if( ! empty($mjob_post->$key) ){
						$project_data_to_export[] = array(
							'name'  => $label,
							'value' => $mjob_post->$key,
						);
					}
				}

				// $project_data_to_export[] = array(
				// 	'name'  => 'Total Reviews',
				// 	'value' => $mjob_post->mjob_total_reviews
				// );

				$item_id = "mjob-{$post->ID}";
				$data_to_export[] = array(
					'group_id'    => 'mjobs',
					'group_label' => __( 'List Mjob' ),
					'item_id'     => $item_id,
					'data'        => $project_data_to_export,
				);
			}
			wp_reset_postdata();
		}
		$done = $found_posts < $number;

	//}
	return array(
		'data' => $data_to_export,
		'done' => $done,
	);

}
function fre_personal_data_exporter( $email_address, $page = 1 ) {
	$export_items = array();
	$user = get_user_by( 'email', $email_address );
	if ( $user && $user->ID ) {

		// Plugins can add as many items in the item data array as they want
		$data = array();
		$role = ae_user_role($user->ID);

		global $wp_query, $ae_post_factory, $post;
		$profile_obj = $ae_post_factory->get('mjob_profile');
		$profile_id = get_user_meta( $user->ID, 'user_profile_id', true );
		$profile = get_post($profile_id);
		$convert    = $profile_obj->convert( $profile );

		$description = !empty($convert->profile_description) ? $convert->profile_description : "";
		$display_name = isset($user_data->display_name) ? $user_data->display_name : '';
		$country_name = isset($convert->tax_input['country'][0]) ? $convert->tax_input['country'][0]->name : '';
		$languages = isset($convert->tax_input['language']) ? $convert->tax_input['language'] : '';

		if ( ! empty( $country_name ) ) {
			$data[] = array(
				'name'  => __( 'Country:', 'enginethemes' ),
				'value' => $country_name
			);
		}
		if ( ! empty( $languages) ) {
			$langs = array();
			if( ! empty($languages) ) {
                foreach($languages as $language) {
                	$langs[] = $language->name;
                }
            }
            if( !empty( $langs ) ){
            	$lns =  implode(",", $langs);
				$data[] = array(
					'name'  => __( 'Languages:', 'enginethemes' ),
					'value' => $lns
				);
			}
		}
		$data[] = array(
			'name'  => __( 'Profile ID', 'enginethemes' ),
			'value' => $profile_id
		);
		$data[] = array(
			'name'  => __( 'URL', 'enginethemes' ),
			'value' =>get_author_posts_url($user->ID)
		);
		$data[] = array(
			'name'  => __( 'Payment info:', 'enginethemes' ),
			'value' => $convert->payment_info
		);
		$data[] = array(
			'name'  => __( 'Billing full name:', 'enginethemes' ),
			'value' => $convert->billing_full_name
		);
		$data[] = array(
			'name'  => __( 'Billing Full Address :', 'enginethemes' ),
			'value' => $convert->billing_full_address
		);
		$data[] = array(
			'name'  => __( 'Billing Country:', 'enginethemes' ),
			'value' => $convert->billing_country
		);
		$data[] = array(
			'name'  => __( 'Billing VAT:', 'enginethemes' ),
			'value' => $convert->billing_vat
		);

		$data[] = array(
			'name'  => __( 'Overview:', 'enginethemes' ),
			'value' => $description
		);
		$user_obj = mJobUser::getInstance();
		$user_data = $user_obj->convert($user);

		// Bank account data
		$bank_first_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['first_name'] : '';
		$bank_middle_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['middle_name'] : '';
		$bank_last_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['last_name'] : '';
		$bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
		$bank_swift_code = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['swift_code'] : '';
		$bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

		// Paypal account data
		$paypal_email = isset($user_data->payment_info['paypal']) ? $user_data->payment_info['paypal'] : '';
		$data[] = array(
			'name'  => __( 'Bank First Name:', 'enginethemes' ),
			'value' => $bank_first_name
		);
		$data[] = array(
			'name'  => __( 'Bank Middle Name:', 'enginethemes' ),
			'value' => $bank_middle_name
		);
		$data[] = array(
			'name'  => __( 'Bank Last Name:', 'enginethemes' ),
			'value' => $bank_last_name
		);
		$data[] = array(
			'name'  => __( 'Bank Name:', 'enginethemes' ),
			'value' => $bank_name
		);
		$data[] = array(
			'name'  => __( 'Bank Swift Name:', 'enginethemes' ),
			'value' => $bank_swift_code
		);
		$data[] = array(
			'name'  => __( 'Bank Account No:', 'enginethemes' ),
			'value' => $bank_account_no
		);
		$data[] = array(
			'name'  => __( 'PayPal Email:', 'enginethemes' ),
			'value' => $paypal_email
		);



		// Add this group of items to the exporters data array.
		$item_id = "mje-info-{$user->ID}";
		$export_items[] = array(
			'group_id'    => 'fre_data',
			'group_label' => 'MJE Data',
			'item_id'     => $item_id,
			'data'        => $data,
		);


	}
	// Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
	//If not it will be called again with $page increased by 1.
	return array(
		'data' => $export_items,
		'done' => true,
	);
}
?>