<?php
require('autoload.php');
global $lumise;

$data = $lumise->connector->get_session('lumise_cart');
$items = isset($data['items']) ? $data['items'] : array();
$fields = array(
    array('email', 'Billing E-Mail'),
    array('address', 'Street Address'),
    array('zip', 'Zip Code'),
    array('city', 'City'),
    array('country', 'Country')
);
$user_data = $lumise->connector->user_data();

$page_title = $lumise->lang('Checkout');
include(theme('header.php'));
?>
        <div class="lumise-bread">
            <div class="container">
                <h1><?php echo $lumise->lang('Checkout'); ?></h1>
            </div>
        </div>
        <form action="<?php echo $lumise->cfg->url;?>process_checkout.php" method="post" class="form-horizontal" id="checkoutform" accept-charset="utf-8">
        <div class="container">
            <div class="row">
                <div id="checkout" class="padding6 span12">
                    <?php if(count($items) > 0):?>
                        <div class="col-md-6 billing">
                            <h3><?php echo $lumise->lang('Billing Information'); ?></h3>
                            <div class="control-group span6">
                                <label for="first_name" class="control-label"><?php echo $lumise->lang('First Name'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="first_name" type="text" value="<?= isset($user_data['first_name'])?$user_data['first_name']:''; ?>" placeholder="Katie" id="first_name" required>
                                </div>
                            </div>
                            <div class="control-group span6 last">
                                <label for="last_name" class="control-label"><?php echo $lumise->lang('Last Name'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="last_name" type="text" placeholder="King" value="<?= isset($user_data['last_name'])?$user_data['last_name']:''; ?>" id="last_name" required>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="email" class="control-label"><?php echo $lumise->lang('Billing E-Mail'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="email" type="email" value="<?= isset($user_data['email'])?$user_data['email']:''; ?>" id="email" required>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="address" class="control-label"><?php echo $lumise->lang('Street Address'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="address" type="text" value="<?= isset($user_data['address'])?$user_data['address']:''; ?>" id="address" required>
                                </div>
                            </div>
                            <div class="control-group span6">
                                <label for="zip" class="control-label"><?php echo $lumise->lang('Postal Code'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="zipcode" type="text" value="<?= isset($user_data['zipcode'])?$user_data['zipcode']:''; ?>" id="zip" required>
                                </div>
                            </div>
                            <div class="control-group span6 last">
                                <label for="city" class="control-label"><?php echo $lumise->lang('City'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="city" type="text" placeholder="" value="<?= isset($user_data['city'])?$user_data['city']:''; ?>" id="city" required>
                                </div>
                            </div>
                            <div class="control-group span6">
                                <label for="country" class="control-label"><?php echo $lumise->lang('Country'); ?><em>*</em></label>
                                <div class="controls">
                                    <select name="country" id="country" required>
                                        <option value=""><?php echo $lumise->lang('Country'); ?></option>
                                        <option value="AR">Argentina</option>
                                        <option value="AU">Australia</option>
                                        <option value="AT">Austria</option>
                                        <option value="BY">Belarus</option>
                                        <option value="BE">Belgium</option>
                                        <option value="BA">Bosnia and Herzegovina</option>
                                        <option value="BR">Brazil</option>
                                        <option value="BG">Bulgaria</option>
                                        <option value="CA">Canada</option>
                                        <option value="CL">Chile</option>
                                        <option value="CN">China</option>
                                        <option value="CO">Colombia</option>
                                        <option value="CR">Costa Rica</option>
                                        <option value="HR">Croatia</option>
                                        <option value="CU">Cuba</option>
                                        <option value="CY">Cyprus</option>
                                        <option value="CZ">Czech Republic</option>
                                        <option value="DK">Denmark</option>
                                        <option value="DO">Dominican Republic</option>
                                        <option value="EG">Egypt</option>
                                        <option value="EE">Estonia</option>
                                        <option value="FI">Finland</option>
                                        <option value="FR">France</option>
                                        <option value="GE">Georgia</option>
                                        <option value="DE">Germany</option>
                                        <option value="GI">Gibraltar</option>
                                        <option value="GR">Greece</option>
                                        <option value="HK">Hong Kong S.A.R., China</option>
                                        <option value="HU">Hungary</option>
                                        <option value="IS">Iceland</option>
                                        <option value="IN" selected>India</option>
                                        <option value="ID">Indonesia</option>
                                        <option value="IR">Iran</option>
                                        <option value="IQ">Iraq</option>
                                        <option value="IE">Ireland</option>
                                        <option value="IL">Israel</option>
                                        <option value="IT">Italy</option>
                                        <option value="JM">Jamaica</option>
                                        <option value="JP">Japan</option>
                                        <option value="KZ">Kazakhstan</option>
                                        <option value="KW">Kuwait</option>
                                        <option value="KG">Kyrgyzstan</option>
                                        <option value="LA">Laos</option>
                                        <option value="LV">Latvia</option>
                                        <option value="LB">Lebanon</option>
                                        <option value="LT">Lithuania</option>
                                        <option value="LU">Luxembourg</option>
                                        <option value="MK">Macedonia</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="MT">Malta</option>
                                        <option value="MX">Mexico</option>
                                        <option value="MD">Moldova</option>
                                        <option value="MC">Monaco</option>
                                        <option value="ME">Montenegro</option>
                                        <option value="MA">Morocco</option>
                                        <option value="NL">Netherlands</option>
                                        <option value="NZ">New Zealand</option>
                                        <option value="NI">Nicaragua</option>
                                        <option value="KP">North Korea</option>
                                        <option value="NO">Norway</option>
                                        <option value="PK">Pakistan</option>
                                        <option value="PS">Palestinian Territory</option>
                                        <option value="PE">Peru</option>
                                        <option value="PH">Philippines</option>
                                        <option value="PL">Poland</option>
                                        <option value="PT">Portugal</option>
                                        <option value="PR">Puerto Rico</option>
                                        <option value="QA">Qatar</option>
                                        <option value="RO">Romania</option>
                                        <option value="RU">Russia</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="RS">Serbia</option>
                                        <option value="SG">Singapore</option>
                                        <option value="SK">Slovakia</option>
                                        <option value="SI">Slovenia</option>
                                        <option value="ZA">South Africa</option>
                                        <option value="KR">South Korea</option>
                                        <option value="ES">Spain</option>
                                        <option value="LK">Sri Lanka</option>
                                        <option value="SE">Sweden</option>
                                        <option value="CH">Switzerland</option>
                                        <option value="TW">Taiwan</option>
                                        <option value="TH">Thailand</option>
                                        <option value="TN">Tunisia</option>
                                        <option value="TR">Turkey</option>
                                        <option value="UA">Ukraine</option>
                                        <option value="AE">United Arab Emirates</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="US">USA</option>
                                        <option value="UZ">Uzbekistan</option>
                                        <option value="VN">Vietnam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group span6 last">
                                <label for="phone" class="control-label"><?php echo $lumise->lang('Phone'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="phone" type="text" value="<?= isset($user_data['phone'])?$user_data['phone']:''; ?>" id="phone" required>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="gst" class="control-label"><?php echo $lumise->lang('GST (if applicable)'); ?></label>
                                <div class="controls">
                                    <input name="gst" type="text" value="" id="gst">
                                </div>
                            </div>
							<hr/>
                            <h3><?php echo $lumise->lang('Shipping Information'); ?> <span style="font-size: small;">Same as billing <input type="checkbox" name="checked" class="shipping_info" checked style="width:auto"></span></h3>
                            <div class="control-group shipping_div span6">
                                <label for="first_name" class="control-label"><?php echo $lumise->lang('First Name'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_first_name" type="text" value="<?= isset($user_data['first_name'])?$user_data['first_name']:''; ?>" placeholder="Katie" id="first_name" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div span6 last">
                                <label for="last_name" class="control-label"><?php echo $lumise->lang('Last Name'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_last_name" type="text" placeholder="King" value="<?= isset($user_data['last_name'])?$user_data['last_name']:''; ?>" id="last_name" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div">
                                <label for="email" class="control-label"><?php echo $lumise->lang('Billing E-Mail'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_email" type="email" value="<?= isset($user_data['email'])?$user_data['email']:''; ?>" id="email" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div">
                                <label for="address" class="control-label"><?php echo $lumise->lang('Street Address'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_address" type="text" value="<?= isset($user_data['address'])?$user_data['address']:''; ?>" id="address" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div span6">
                                <label for="zip" class="control-label"><?php echo $lumise->lang('Postal Code'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_zipcode" type="text" value="<?= isset($user_data['zipcode'])?$user_data['zipcode']:''; ?>" id="zip" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div span6 last">
                                <label for="city" class="control-label"><?php echo $lumise->lang('City'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_city" type="text" placeholder="" value="<?= isset($user_data['city'])?$user_data['city']:''; ?>" id="city" required>
                                </div>
                            </div>
                            <div class="control-group shipping_div span6">
                                <label for="country" class="control-label"><?php echo $lumise->lang('Country'); ?><em>*</em></label>
                                <div class="controls">
                                    <select name="shipping_country" id="country" required>
                                        <option value=""><?php echo $lumise->lang('Country'); ?></option>
                                        <option value="AR">Argentina</option>
                                        <option value="AU">Australia</option>
                                        <option value="AT">Austria</option>
                                        <option value="BY">Belarus</option>
                                        <option value="BE">Belgium</option>
                                        <option value="BA">Bosnia and Herzegovina</option>
                                        <option value="BR">Brazil</option>
                                        <option value="BG">Bulgaria</option>
                                        <option value="CA">Canada</option>
                                        <option value="CL">Chile</option>
                                        <option value="CN">China</option>
                                        <option value="CO">Colombia</option>
                                        <option value="CR">Costa Rica</option>
                                        <option value="HR">Croatia</option>
                                        <option value="CU">Cuba</option>
                                        <option value="CY">Cyprus</option>
                                        <option value="CZ">Czech Republic</option>
                                        <option value="DK">Denmark</option>
                                        <option value="DO">Dominican Republic</option>
                                        <option value="EG">Egypt</option>
                                        <option value="EE">Estonia</option>
                                        <option value="FI">Finland</option>
                                        <option value="FR">France</option>
                                        <option value="GE">Georgia</option>
                                        <option value="DE">Germany</option>
                                        <option value="GI">Gibraltar</option>
                                        <option value="GR">Greece</option>
                                        <option value="HK">Hong Kong S.A.R., China</option>
                                        <option value="HU">Hungary</option>
                                        <option value="IS">Iceland</option>
                                        <option value="IN" selected>India</option>
                                        <option value="ID">Indonesia</option>
                                        <option value="IR">Iran</option>
                                        <option value="IQ">Iraq</option>
                                        <option value="IE">Ireland</option>
                                        <option value="IL">Israel</option>
                                        <option value="IT">Italy</option>
                                        <option value="JM">Jamaica</option>
                                        <option value="JP">Japan</option>
                                        <option value="KZ">Kazakhstan</option>
                                        <option value="KW">Kuwait</option>
                                        <option value="KG">Kyrgyzstan</option>
                                        <option value="LA">Laos</option>
                                        <option value="LV">Latvia</option>
                                        <option value="LB">Lebanon</option>
                                        <option value="LT">Lithuania</option>
                                        <option value="LU">Luxembourg</option>
                                        <option value="MK">Macedonia</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="MT">Malta</option>
                                        <option value="MX">Mexico</option>
                                        <option value="MD">Moldova</option>
                                        <option value="MC">Monaco</option>
                                        <option value="ME">Montenegro</option>
                                        <option value="MA">Morocco</option>
                                        <option value="NL">Netherlands</option>
                                        <option value="NZ">New Zealand</option>
                                        <option value="NI">Nicaragua</option>
                                        <option value="KP">North Korea</option>
                                        <option value="NO">Norway</option>
                                        <option value="PK">Pakistan</option>
                                        <option value="PS">Palestinian Territory</option>
                                        <option value="PE">Peru</option>
                                        <option value="PH">Philippines</option>
                                        <option value="PL">Poland</option>
                                        <option value="PT">Portugal</option>
                                        <option value="PR">Puerto Rico</option>
                                        <option value="QA">Qatar</option>
                                        <option value="RO">Romania</option>
                                        <option value="RU">Russia</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="RS">Serbia</option>
                                        <option value="SG">Singapore</option>
                                        <option value="SK">Slovakia</option>
                                        <option value="SI">Slovenia</option>
                                        <option value="ZA">South Africa</option>
                                        <option value="KR">South Korea</option>
                                        <option value="ES">Spain</option>
                                        <option value="LK">Sri Lanka</option>
                                        <option value="SE">Sweden</option>
                                        <option value="CH">Switzerland</option>
                                        <option value="TW">Taiwan</option>
                                        <option value="TH">Thailand</option>
                                        <option value="TN">Tunisia</option>
                                        <option value="TR">Turkey</option>
                                        <option value="UA">Ukraine</option>
                                        <option value="AE">United Arab Emirates</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="US">USA</option>
                                        <option value="UZ">Uzbekistan</option>
                                        <option value="VN">Vietnam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group shipping_div span6 last">
                                <label for="phone" class="control-label"><?php echo $lumise->lang('Phone'); ?><em>*</em></label>
                                <div class="controls">
                                    <input name="shipping_phone" type="text" value="<?= isset($user_data['phone'])?$user_data['phone']:''; ?>" id="phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 order_overview">
                            <h3>Order Review</h3>
                            <div class="wrap-table">
                                <table class="lumise-table sty2">
                                    <thead>
                                        <tr>
                                            <th><?php echo $lumise->lang('Product Name'); ?></th>
                                            <th><?php echo $lumise->lang('Thumbnails'); ?></th>
                                            <th><?php echo $lumise->lang('Attributes'); ?></th>
                                            <th><?php echo $lumise->lang('Qty'); ?></th>
                                            <th class="text-right"><?php echo $lumise->lang('Subtotal'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach($items as $item):
                                        
                                            $cart_data = $lumise->lib->get_cart_item_file($item['file']);
                                            $meta = $lumise->lib->cart_meta($cart_data);
                                            $item = array_merge($item, $cart_data);
                                        
                                            ?>
                                        <tr>
                                            <td><?php echo $item['product_name'];?></td>
                                            <td>
                                                <?php

                                                if(count($item['screenshots'])> 0):
                                                    foreach($item['screenshots'] as $image):?>
                                                        <img width="150" src="<?php echo $lumise->cfg->upload_url.$image; ?>" />
                                                    <?php endforeach;
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                
                                                <?php foreach($meta as $me): ?>
                                                    <p>
                                                        <strong><?php echo $me['name']; ?></strong> : <?php echo $me['value']; ?>
                                                    </p>
                                                <?php endforeach;?>
                                                
                                            </td>
                                            <td><?php echo $item['qty'];?></td>
                                            <td class="text-right"><?php echo $lumise->lib->price($item['price']['total']);?><?php $total += $item['price']['total'];?></td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong><?php echo $lumise->lang('Sub Total'); ?></strong></td>
                                            <td class="text-right"><?php echo $lumise->lib->price($total);?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right"><strong><?php echo $lumise->lang('Grand Total'); ?></strong></td>
                                            <td class="text-right"><?php $grand_total = $total;?><?php echo $lumise->lib->price($grand_total);?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="control-group span12 comment">
                                <label for="comment" class="control-label"><?php echo $lumise->lang('Comments'); ?></label>
                                <div class="controls">
                                    <textarea name="comment" type="text" value="" id="comment"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="action" value="placeorder">
                            <div class="control-group last payments">
                                <h3>Payment</h3>
                                <div class="controls">
                                    <div class="lumise-payment-item">
                                        <input name="payment" type="radio" value="cod" id="payment-cod" required>
                                        <label for="payment-cod">Bill to company</label>
                                    </div>
                                    <div class="lumise-payment-item">
                                        <input name="payment" type="radio" value="paypal" id="payment-paypal" required>
                                        <label for="payment-paypal"><img src="<?php echo $lumise->cfg->url.'assets/images/paypal.png'; ?>" alt="<?php echo $lumise->lang('Paypal payment'); ?>"/><?php echo $lumise->lang('Paypal'); ?></label>
                                    </div>
                                    <label for="payment" class="error"></label>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button name="submit" type="submit" class="btn btn-large btn-primary"><?php echo $lumise->lang('Place Order'); ?></button>
                            </div>
                        </div>
                        
                    <?php else:?>
                        <div class="span12">
                            <p><?php echo $lumise->lang('Your cart is currently empty.'); ?></p>
                        </div>
                        <div class="form-actions">
                            <a href="<?php echo $lumise->cfg->url;?>" class="btn btn-large btn-primary"><?php echo $lumise->lang('Continue Shopping'); ?></a>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        </form>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#checkoutform").validate();
			$('.shipping_div').css('display', 'none');
			$( "input[name^='shipping']" ).attr('disabled', 'disabled');
			$(".shipping_info").change(function () {
				if($(this).prop('checked') == true){
					$('.shipping_div').css('display', 'none');
					$( "input[name^='shipping']" ).attr('disabled', 'disabled');
				}else{
					$( "input[name^='shipping']" ).attr('disabled', false);
					$('.shipping_div').css('display', 'block');
				}
			});
        });
        </script>
<?php
include(theme('footer.php'));
//update cart info
if(!isset($grand_total)){
    $grand_total = 0;
}
$data['total'] = $grand_total;
$lumise->connector->set_session('lumise_cart', $data);
