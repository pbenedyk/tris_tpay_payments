<?php

global $table_prefix, $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = 'tris_payments';
$wp_table = $table_prefix . "$table_name";
if ($wpdb->get_var("show tables like '$wp_table'") != $wp_table) :
    $sql = "CREATE TABLE `" . $wp_table . "` ( ";
    $sql .= " `id` int(11) NOT NULL auto_increment, ";
    $sql .= " `camp_id` varchar(200) NOT NULL, ";
    $sql .= " `name` varchar(200) NOT NULL, ";
    $sql .= " `lastname` varchar(200) NOT NULL, ";
    $sql .= " `email` varchar(200) NOT NULL, ";
    $sql .= " `amount` float NOT NULL, ";
    $sql .= " `create_date` datetime NOT NULL, ";
    $sql .= " `payment_confirm` varchar(200) NOT NULL, ";
    $sql .= " PRIMARY KEY `id` (`id`) ";
    $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    dbDelta($sql);
endif;
