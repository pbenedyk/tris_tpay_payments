<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Payment_List_Table extends WP_List_Table
{
    public function get_columns()
    {
        $table_columns = array(
            'id'        => 'ID',
            'camp_id' => 'Numer Kampanii',
            'name'    => 'Imię',
            'lastname'  => 'Nazwisko',
            'email'        => 'E-mail',
            'amount' => 'Kwota',
            'create_date' => 'Data wpłaty',
            'payment_confirm' => 'Status potwierdzenia <br/><span class="tableInfo">0 - czeka na potwierdzenie</span><br><span class="tableInfo">1 - potwierdzono</span>'

        );
        return $table_columns;
    }

    public function prepare_items()
    {
        global $wpdb;

        $sql = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tris_payments ORDER BY create_date DESC"), ARRAY_A);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $sql;
    }
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
}
add_action('admin_head', 'tris_admin_style');
function tris_admin_style()
{
    wp_enqueue_style('admin-style', plugins_url() . '/tris_payments/assets/admin_style.css',);
}


add_action('admin_menu', 'tris_payment_plugin_setup_menu');

function tris_payment_plugin_setup_menu()
{
    add_menu_page('Płatności TRIS', 'Płatności TRIS', 'manage_options', 'tris_payment', 'tris_payment_admin_page', 'dashicons-money-alt');
    add_submenu_page('tris_payment', 'Ustawienia Płatności TRIS', 'Ustawienia Płatności TRIS', 'manage_options', 'tris_payment_settings', 'tris_payment_admin_settings_page');
    register_setting(
        'tris_payment', // option group
        'tpay_id'
    );
    register_setting(
        'tris_payment', // option group
        'tpay_PIN'
    );
    register_setting(
        'tris_payment', // option group
        'default_camp_id'
    );
}
add_action('admin_init', 'tris_payment_settings_register');
function tris_payment_settings_register()
{
    add_settings_field('tpay_id', 'tPay IDs',  'tris_payment_settings_save', 'tris_payment');
}

function tris_payment_admin_page()
{
    echo '<style>.wp-list-table .column-id { width: 5%; }</style>';
    $wp_list_table = new Payment_List_Table();
    echo '<div class="wrap"><h2>Lista Płatności TRIS</h2>';
    $wp_list_table->prepare_items();
    $wp_list_table->display();
    echo '</div>';
}
function tris_payment_admin_settings_page()
{
    if (!empty($_POST)) {
        update_option('default_camp_id', $_POST['default_camp_id']);
        update_option('tpay_id', $_POST['tpay_id']);
        update_option('tpay_pin', $_POST['tpay_pin']);
?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Zapisano!', 'sample-text-domain'); ?></p>
        </div>
<?php
    }
    echo '<div class="wrap"><h2>Ustawienia Płatności TRIS</h2>';
    echo '<div style="font-size:14px; line-height:1.8">';
    echo '<br/><b>INFORMACJE:</b> <br/>';
    echo '1) Wypełnij poniższe pola uzupełniając je danymi z twojego konta tPay<br/>';
    echo '2) Aby dodać formularz użyj shortcode <b>[payment_form]</b><br/>';
    echo '3) Strona DZ powinna być na adresie <a target="_blank" href="' . get_option('siteurl') . '/dziekujemy_za_wplate">' . get_option('siteurl') . '/<b style="color:red">dziekujemy_za_wplate</b></a> (<- kliknij i sprawdź)<br/>';
    echo '4) Aby użyć innego niż domyśny numer kampanii należy linkować do strony w następujący sposób: <a target="_blank" href="' . get_option('siteurl') . '/jakas_strona_z formularzem/?caID=0123453">'  . get_option('siteurl') . '/jakas_strona_z_formem/<b style="color:red">?caID=0123456</b></a><br/>';
    echo '5) Zbieraj wpłaty sprawdzaj je na stronie ustawień <b><a href="' . get_option('siteurl') . '/wp-admin/admin.php?page=tris_payment"> "Płatności TRIS" </a></b> i ciesz się życiem!';
    echo '</div>';
    echo '<br/><br/>';
    echo '<form method="post">';

    echo '<b>Domyślna kampania: </b><br/><input type="text" name="default_camp_id" value="' . get_option('default_camp_id', '') . '"/><br/><br/>';
    echo '<b>tPay ID: </b><br/><input type="text" name="tpay_id" value="' . get_option('tpay_id', '') . '"/><br/><br/>';
    echo '<b>tPay PIN: </b><br/><input type="password" name="tpay_pin" value="' . get_option('tpay_pin', '') . '"/><br/><br/>';
    echo '<button class="button" type="submit">Zapisz</button>';
    echo '</form>';
    echo '</div>';
}
