<?php

/**
 * Plugin Name: Płatności Tpay for TRIS
 * Plugin URI: http://trispayments.php
 * Description: Obsługa płatności tPay dla TRIS
 * Version: 1.0.0
 * Author: Piotr Benedyk
 * Author URI: http://piotrbenedyk.pl
 * License: GPL2
 */
function init_table()
{
    include('init/init_db.php');
}
register_activation_hook(__FILE__, 'init_table');

require_once('functions.php');
require_once('admin/admin_page.php');
if (!is_admin()) {
    require_once('shortcodes/payment_form.php');
}
