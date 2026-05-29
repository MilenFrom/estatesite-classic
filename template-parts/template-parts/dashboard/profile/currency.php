<?php
if( houzez_option('multi_currency') == 1 && houzez_check_role() && class_exists('Houzez_Currencies') ) { ?>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e( 'Choose Currency', 'houzez' ); ?></h2>
    </div>
    <div class="block-content-wrap">
        <form>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <?php wp_nonce_field( 'houzez_user_currency_ajax_nonce', 'houzez-user-currency-security-pass' );   ?>
                        <select name="houzez_user_currency" id="houzez_user_currency" class="selectpicker form-control" data-live-search="false" data-live-search-style="begins" title="">
                            <option value=""><?php esc_html_e( 'Default Currency', 'houzez' ); ?></option>
                            <?php
                            $user_default_currency  =   get_the_author_meta( 'fave_author_currency' , get_current_user_id() );
                            $user_currency = $user_default_currency;

                            $currencies = Houzez_Currencies::get_currency_codes();
                            if($currencies) {
                                foreach ($currencies as $currency) {
                                    echo '<option '.selected( $currency->currency_code, $user_currency, false).' value="'.$currency->currency_code.'">'.$currency->currency_code.'</option>'; 
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>