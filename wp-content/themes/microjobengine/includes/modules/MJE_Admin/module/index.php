<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 13:25
 */
require_once dirname(__FILE__) . '/welcome/mjob-welcome.php';
 
require_once dirname(__FILE__) . '/settings/user.php';
require_once dirname(__FILE__) . '/settings/microjob.php';
require_once dirname(__FILE__) . '/settings/currency.php';
require_once dirname(__FILE__) . '/settings/payment-type.php';
require_once dirname(__FILE__) . '/settings/withdraw-config.php';
require_once dirname(__FILE__) . '/settings/seo.php';
require_once dirname(__FILE__) . '/settings/translations.php';

require_once dirname(__FILE__) . '/payment-gateways/index.php';

//Member list
require_once dirname( __FILE__ ).'/member-list/container-users.php';
//
require_once dirname( __FILE__ ).'/package-purchases/container-payments.php';