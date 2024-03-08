<?php
    global $post, $ae_post_factory;
    $post_object = $ae_post_factory->get( 'mjob_extra' );
    $current = $post_object->convert( $post );
?>
<li class="extra-item" data-id="<?php echo $current->ID; ?>">
    <div class="form-group list-item-extra">
        <div class="packge-chose">
            <div class="checkbox">
                <label>
                    <input data-id="<?php echo $current->ID; ?>" type="checkbox" value="<?php echo $current->et_budget; ?>" name="mjob_extra">
                    <span><?php echo $current->post_title; ?></span>
                </label>
            </div>
        </div>
        <div class="package-price mje-price-text"><?php echo $current->et_budget_text; ?></div>
    </div>
</li>