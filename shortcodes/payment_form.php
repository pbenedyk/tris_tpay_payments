<?php

add_shortcode('payment_form', 'render_payment_form');

function render_payment_form()
{
  wp_enqueue_script('tris_js_script');
  wp_enqueue_style('tris_payments_style');
  wp_enqueue_script('tris_validator');
  if(isset($_GET['caID'])){
    $camp_id = $_GET['caID'];
  }else{
    $camp_id = get_option('default_camp_id', '');
  }
  return '
  <div class="col-md-8 col-xs-12 formBox clearfix col-centered"> 
    <form method="POST" action="https://secure.tpay.com" id="formularz">
      ' . wp_nonce_field("paynonce", '_wpnonce', true, false) . '
        <input class="amountChoose" data-amount="30" type="button" id="dp_def_amount" value="30.00 zł">
        <input class="amountChoose" data-amount="100" type="button" id="dp_def_amount" value="100.00 zł">
        <input class="amountChoose" data-amount="300" type="button" id="dp_def_amount" value="300.00 zł">
        <input class="amountChoose" type="hidden" name="type" value="0">
        <input class="amountChoose" type="hidden" name="currency" value="PLN">
          <input class="amountChoose" type="button" id="dp_other_amount" value="Inna kwota">
          <br/>
      <div class="amountContainer" style="width:100%; margin:30px auto; text-align:center ">
          <span id="dp_other_amount_txt"><span style="color:#fff">Wpisz tu kwotę</span></span>:
          <input type="text" name="amount" id="dp_kwota" 
          pattern="^([1-9])((\.\d{1,2})?)$|^((?!0)(\d){1,5})((\.\d{1,2})?)$|^(1(\d{5})(.\d{1,2})?)$|^(200000(.[0]{1,2})?)$" 
          placeholder="np. 100" maxlength="9" size="9" title="Kwota powinna zawierać się w przedziale 1 - 200000 PLN. Dozwolony format to np: 100 lub 152.43" 
          >PLN
      </div>
      <input type="hidden" name="camp_id" value="'.$camp_id.'"/>
      <input type="hidden" name="id" value="'.get_option('tpay_id', '').'"/>
      <input type="hidden" name="type" value="0"/>
      <input type="hidden" name="kraj" value="Polska"/>
      <input id="URL_back" type="hidden" name="return_url" value="' . get_option('siteurl') . '/dziekujemy_za_wplate/?k="/>
      <input type="hidden" name="result_url" value="' . get_option('siteurl') . '/checkPayment/"/>
      <input type="hidden" id="md5sum" name="md5sum" value=""/>
      <input type="hidden" name="description" value="Wsparcie ze strony "/>
      <input id="crc" type="hidden" name="crc" value=""/>
      <input type="hidden" id="fullname" name="name" value=""/>
      <div class="form-item"> 
        <input required type="text" class="form-control" data-type="name" name="firstname" id="name" placeholder="Imię">
      </div>
      <div class="form-item"> 
        <input required type="text" class="form-control" data-type="lastname" name="lastname" id="lastname" placeholder="Nazwisko">
      </div>
      <div id="emailBox928" class="form-item"> 
        <input required type="email" name="email" data-type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Adres email">
      </div>
      <div class="form-item">
        <input required type="checkbox" class="form-check-input col-xs-1" id="giodocheck">
        <label class="form-check-label col-xs-11 no-padding payform-label-check" for="giodocheck">Wyrażam zgodę na otrzymywanie informacji drogą elektroniczną</label>
      </div>
      <div class="clearfix"></div>
      <div class="form-item"> 
        <button type="submit" class="btn col-md-12 submitButton">WSPIERAM</button>
      </div>
    </form>
  </div>';
}
