<?php

add_action("wp_enqueue_scripts", "tris_scripts");
function tris_scripts()
{
    wp_register_script(
        'tris_js_script',
        plugins_url() . '/tris_payments/assets/main.js',
        array('jquery'),
        false,
        false
    );

    wp_register_script(
        'tris_validator',
        'https://validator.piotrskarga.pl/ajax.js',
        array('jquery'),
        false,
        false
    );


    wp_register_style(
        'tris_payments_style',
        plugins_url() . '/tris_payments/assets/main.css'
    );
}
add_action('wp_ajax_make_payment', 'makePayment');
add_action('wp_ajax_nopriv_make_payment', 'makePayment');

function makePayment()
{
    $params = array();
    parse_str($_POST['form_data'], $params);
    if (!(wp_verify_nonce($params['_wpnonce'], 'paynonce'))) {
        echo json_encode(array('error' => 'Nonce invalid!'));
        exit;
    }
    global $table_prefix, $wpdb;
    $table_name = 'tris_payments';
    $wp_table = $table_prefix . "$table_name";

    if ($wpdb->insert(
        $wp_table,
        array(
            'name' => $params['firstname'],
            'lastname' => $params['lastname'],
            'email' => $params['email'],
            'camp_id' => $params['camp_id'],
            'amount' => $params['amount'],
            'create_date' => date('Y-m-d H:i:s'),
            'payment_confirm' => 0,
        )
    ) === FALSE) {
        echo "Error!!";
        echo $wpdb->print_error();
        echo $wpdb->last_query;
    } else {
        $id = get_option('tpay_id', '');
        $amount = $params['amount'];
        $crc = base64_encode($wpdb->insert_id);
        $PIN = get_option('tpay_pin', '');
        $md5sum = md5(implode('&', [$id, $amount, $crc, $PIN]));
        echo json_encode(
            array(
                'md5' => $md5sum,
                'crc' => $crc
            )
        );
    }
    exit;
}

add_action('init', 'payment_confirm_resource');
function payment_confirm_resource()
{
    global $wpdb;
    if ($_SERVER["REQUEST_URI"] == '/checkPayment' || $_SERVER["REQUEST_URI"] == '/checkPayment/') {
        // Check IP address and POST parameters
        $ipTable = array(
            '195.149.229.109', '148.251.96.163', '178.32.201.77',
            '46.248.167.59', '46.29.19.106', '176.119.38.175'
        );

        if (in_array($_SERVER['REMOTE_ADDR'], $ipTable) && !empty($_POST)) {
            $transactionStatus = $_POST['tr_status'];
            $error = $_POST['tr_error'];
            $CRC = $_POST['tr_crc'];
            if ($transactionStatus == 'TRUE' && $error == 'none') {
                $id = base64_decode($CRC);
                $where = ['id' => $id];
                $updated = $wpdb->update($wpdb->prefix . 'tris_payments', array('payment_confirm' => 1), $where);
                echo 'TRUE';
            } else {
                echo 'TRUE';
            }
        } else {
            echo 'FALSE - Invalid request';
        }
        die();
    }
}
