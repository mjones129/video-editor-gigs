<?php
function filterCustomerData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (  !empty($strstr)  &&  strstr($str, '"') )
        $str = '"' . str_replace('"', '""', $str) . '"';
}
Class AE_Export_Excel{
	public $file_name;
	public $type;
	function __construct($type = 'xls'){

		$this->type = $type;

		if( $this->type !== 'xls' )
			$this->type = 'xlsx';

		$this->file_name = $this->get_filename();
	}

	function get_filename(){

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		$date        = gmdate( 'Y-m-d' );
        $wp_filename = $sitename . 'mjeOrder.' . $date . '.'.$this->type;
        $upload_dir = wp_upload_dir();
        $file_path  = $wp_filename;
        return $wp_filename;
	}

	function post_fields(){
		return array('post_title','order_date','post_status','post_status');
	}
	function meta_fields(){
		$fields = array('payment_type','real_amount','currency');
		return $fields;
	}
	function convert_currecy_to_string($currency){

		if( is_array($currency) ){
			return $currency['code'].'('.$currency['icon'].')';
		}
	}
	function set_header($file_name){

	}
	function download($args, $filepath = ''){
		$file_name = $this->file_name;
		if( !empty($file_path) )
			$file_name = $file_path;
		$type = $this->type;


		if($type == 'xls'){
			header("Content-Disposition: attachment; filename=\"$file_name\"");
			header("Content-Type: application/vnd.ms-excel");
		} else{
			//header('Content-Description: File Transfer');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename='.basename($file_name));
    		header('Content-Transfer-Encoding: binary');
			//header('Expires: 0');
		    //header('Cache-Control: must-revalidate');
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    //header('Pragma: public');
		}

		$query = new WP_Query($args);
		$posts  = array();
		if( $query->have_posts() ){
			while($query->have_posts() ){
				$query->the_post();
				global $post;

				$p_id = $post->ID;

				$temp 	= array();
				$fields = $this->post_fields();
				$buyer 					= get_userdata($post->post_author);
				$temp['mjob_order'] 	= $post->post_title;
				$temp['order_date'] 	= $post->post_date;
				$temp['order_status'] 	= $post->post_status;
				$temp['buyer']  		= $post->post_author;
				if( $buyer && !is_wp_error($buyer) )
				$temp['buyer'] 			= $buyer->display_name;

				$meta_fields = $this->meta_fields();

				foreach($meta_fields as $field_name){
					$temp[$field_name] = get_post_meta($p_id,$field_name, true);
				}
				$temp['currency']	= $this->convert_currecy_to_string($temp['currency']);
;
				$posts[] = $temp;
			}
		}
		$column_names  = false;

		//To define column name in first row.
		if($posts){

			$column_names = false;
			// run loop through each row in $customers_data
			foreach ($posts as $row) {

			    if (!$column_names) {
			        echo implode("\t", array_keys($row)) . "\n";
			        $column_names = true;
			    }
			    // The array_walk() function runs each array element in a user-defined function.

			    array_walk($row, 'filterCustomerData');
			    $value = array_values($row);
			    echo implode("\t", $value) . "\n";

			}
		}

		exit;
	}

	function testData(){
		$customers_data = array(
		    array(
		        'customers_id' => '1',
		        'customers_firstname' => 'Chris',
		        'customers_lastname' => 'Cavagin',
		        'customers_email' => 'chriscavagin@gmail.com',
		        'customers_telephone' => '9911223388'
		    ),
		    array(
		        'customers_id' => '2',
		        'customers_firstname' => 'Richard',
		        'customers_lastname' => 'Simmons',
		        'customers_email' => 'rsimmons@media.com',
		        'customers_telephone' => '9911224455'
		    ),

		);
		return $customers_data;
	}

}