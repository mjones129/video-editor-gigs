<?php

/*
Lỗi: một số trường hợp nếu dùng paypal standar account thì paypal không reddirect trang process payment mà redirect về trang chủ.
Lỗi cụ thể: Do khách chưa bật  data tranfer lên, dẫn tới order bị pending, mà pending thì paypal không redirect về url mong muốn.

Kiêm tra thêm giá trị pending_reason để biết.
Dẫn tới không gọi được hook
if( $process_type == 'buy' ){
 $payment_return = MJE_Checkout::process_payment( $payment_type, $session ); // buy 1 service
}

returnURL:  https://home_url.net/?page_id=303&order-id=382&ppipn=engineipn1&ppipn=ppappengineipn2
returnURL:  https://home_url.net/?page_id=303&order-id=387&ppipn=engineipn1&ppipn=ppappengineipn2
notify_url: https://home_url.net/?paypalListener=paypal_appengine_IPN
*/

function ae_veryfy_paypal_standard(){
	if( isset($_REQUEST['paypalListener'] ) && $_REQUEST['paypalListener']  == 'paypal_appengine_IPN' ) {
		et_track_payment('catching notiry_url reponse - paypalListener: ');
		$order_id 		= $_POST['custom'];
		$porder = get_post($order_id);
		if( $porder->post_status !== 'publish' ){

			if( isset($session['process_type']) && $session['process_type'] == 'buy' ){
				$pending_reason = $_POST['pending_reason'];
				if($pending_reason){
					et_track_payment('pending reason :'.$pending_reason);
				}

				//unilateral: The payment is pending because it was made to an email address that is not yet registered or confirmed

				$order 		= new MJE_Order($order_id);
				$order_data = $order->get_order_data();
				$payment_type = $order_data['payment'];

				$receiver_email = $_POST['receiver_email'];
				$payment_gross 	= $_POST['payment_gross'];
				$mc_currency 	= $_POST['mc_currency'];
				$payer_status 	= $_POST['payer_status'];
			}



			//$order 		= new MJE_Order($order_id);
			//MJE_Checkout::process_payment( $payment_type, $session ); // buy 1 service
		} else{
			et_track_payment('This is business account.');
			et_track_payment($_POST);
		}
	}
}
add_action('parse_request','ae_veryfy_paypal_standard',1);

/**
[mc_gross] => 5.00
    [invoice] => kkk_392
    [protection_eligibility] => Ineligible
    [address_status] => confirmed
    [item_number1] => 1
    [payer_id] => 3AN8SZT3J95GQ
    [address_street] => 1 Main St
    [payment_date] => 06:34:49 Mar 08, 2022 PST
    [payment_status] => Pending
    [charset] => windows-1252
    [address_zip] => 95131
    [first_name] => Employer
    [address_country_code] => US
    [address_name] => Employer Job\'s Test Store
    [notify_version] => 3.9
    [custom] => 392
    [payer_status] => verified
    [address_country] => United States
    [num_cart_items] => 1
    [address_city] => San Jose
    [verify_sign] => AJGJI6tt5tG5EMBDeammzsGWQTCyAD7nH5MzUwLSa2ypIBrfA.e46X7E
    [payer_email] => employer@etteam.com
    [txn_id] => 5SK31951UJ491154H
    [payment_type] => instant
    [payer_business_name] => Employer Job\'s Test Store
    [last_name] => Job
    [item_name1] => Order for I can cook
    [address_state] => CA
    [receiver_email] => danhoat@et.com
    [shipping_discount] => 0.00
    [quantity1] => 1
    [insurance_amount] => 0.00
    [pending_reason] => unilateral
    [txn_type] => cart
    [discount] => 0.00
    [mc_gross_1] => 5.00
    [mc_currency] => USD
    [residence_country] => US
    [test_ipn] => 1
    [shipping_method] => Default
    [transaction_subject] =>
    [payment_gross] => 5.00
    [ipn_track_id] => 408c4580fe47b
)


BUSINESS RESPONSE
(
    [mc_gross] => 5.00
    [invoice] => kkk_394
    [protection_eligibility] => Eligible
    [address_status] => confirmed
    [item_number1] => 1
    [payer_id] => 3AN8SZT3J95GQ
    [address_street] => 1 Main St
    [payment_date] => 06:38:58 Mar 08, 2022 PST
    [payment_status] => Completed
    [charset] => windows-1252
    [address_zip] => 95131
    [first_name] => Employer
    [mc_fee] => 0.66
    [address_country_code] => US
    [address_name] => Employer Job\'s Test Store
    [notify_version] => 3.9
    [custom] => 394
    [payer_status] => verified
    [business] => webowner@et.com
    [address_country] => United States
    [num_cart_items] => 1
    [address_city] => San Jose
    [verify_sign] => AB87G.YcQCEL6BVlgTX1dXnBszoZARjatjHMky9txx4KhLOG9kT4xX-h
    [payer_email] => employer@etteam.com
    [txn_id] => 89807166PP522354M
    [payment_type] => instant
    [payer_business_name] => Employer Job\'s Test Store
    [last_name] => Job
    [address_state] => CA
    [item_name1] => Order for I can cook
    [receiver_email] => webowner@et.com
    [payment_fee] => 0.66
    [shipping_discount] => 0.00
    [quantity1] => 1
    [insurance_amount] => 0.00
    [receiver_id] => Z78VLBZT7FN6G
    [txn_type] => cart
    [discount] => 0.00
    [mc_gross_1] => 5.00
    [mc_currency] => USD
    [residence_country] => US
    [test_ipn] => 1
    [shipping_method] => Default
    [transaction_subject] =>
    [payment_gross] => 5.00
    [ipn_track_id] => 9a0930be17032
)
*/