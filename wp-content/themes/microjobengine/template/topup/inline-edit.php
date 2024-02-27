<script type="text/template" id="topup-inline-edit-template">
    <td colspan="6" id="inline-edit-topup-{{= row_id }}" class="colspanchange">
        <div class="inline-edit-spinner hide">
            <span class="spinner"></span>
        </div>
        <fieldset class="inline-edit-col-left topup-edit-user-info">
            <legend class="inline-edit-legend"><?php _e( 'Information', 'enginethemes' ); ?></legend>
            <div class="inline-edit-col">
                <p><?php _e( 'Username', 'enginethemes' ); ?><br><strong>{{= user_login }}</strong></p>
                <p><?php _e( 'Email', 'enginethemes' ); ?><br><strong>{{= user_email }}</strong></p>
                <p><?php _e( 'Date registered', 'enginethemes' ); ?><br><strong>{{= user_registered }}</strong></p>
                <p><?php _e( 'Available fund', 'enginethemes' ); ?><br></p>
                <p>
                    <span class="topup-edit-available-fun-preview">{{= user_fund }}</span>
                    <# if (typeof old_balance_html != 'undefined') { #>
                        <span class="topup-edit-available-fun-preview old">{{= old_balance_html }}</span>
                    <# } #>
                </p>
            </div>
        </fieldset>

        <fieldset class="inline-edit-col-center topup-edit-form">
            <legend class="inline-edit-legend"><?php _e( 'Edit Credit Top-up', 'enginethemes' ); ?></legend>
            <div class="inline-edit-col">
                <label for="topup-mode-add">
                    <input type="radio"
                        name="topup-mode"
                        class="topup-mode"
                        id="topup-mode-add"
                        value="add"
                        <# if (mode == 'add') { #>checked<# } #>
                    >
                    <?php _e( 'Add fund' ); ?>
                </label>

                <label for="topup-mode-minus">
                    <input type="radio"
                        name="topup-mode"
                        class="topup-mode"
                        id="topup-mode-minus"
                        value="minus"
                        <# if (mode == 'minus') { #>checked<# } #>
                    >
                    <?php _e( 'Deduct fund' ); ?>
                </label>

                <?php
                    $currency = mje_get_currency();
                ?>
                <label for="topup-amount"><?php _e( 'Amount', 'enginethemes'); ?> (<?php echo $currency['icon']; ?>)</label>
                <input type="text"
                    class="topup-amount-input widefat"
                    name="topup-amount"
                    id="topup-amount"
                    placeholder="0.00"
                    <# if (amount != '') { #>value="{{= amount }}"<# } #>
                >

                <label for="topup-message"><?php _e( 'Message (optional)', 'enginethemes'); ?></label>
                <textarea name="topup-message" class="widefat" id="topup-message" rows="5" placeholder="<?php _e( 'Give your user the message to explain why you add or deduct their fund.', 'enginethemes' ) ?>"></textarea>

                <div class="topup-edit-actions">
                    <button class="button topup-edit-cancel" data-id="{{= row_id }}"><?php _e( 'Cancel', 'enginethemes' ); ?></button>
                    <button class="button button-primary topup-edit-save"
                    <# if (amount == '') { #>disabled<# } #>
                    data-id="{{= row_id }}">
                        <?php _e( 'Save', 'enginethemes' ); ?>
                    </button>
                </div>
            </div>
        </fieldset>
    </td>
</script>