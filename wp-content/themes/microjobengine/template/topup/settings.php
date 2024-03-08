<?php
    $topup_table->prepare_items();
?>
<div class="wrap" id="js-credit-topup">
    <h2><?php echo TOPUP_SINGULAR; ?>
        <?php if( ! empty( $_GET['s'] ) ) : ?>
        <span class="subtitle"><?php printf( __( 'Search results for "%s"' ), $_GET['s'] ); ?></span>
        <?php endif; ?>
    </h2>
    <form method="GET">
        <input type="hidden" name="page" value="credit-topup.php">
        <?php
            $topup_table->search_box( __( 'Search Users' ), 'user' );
            $topup_table->render_roles_filter();
            $topup_table->display();
        ?>
    </form>
</div>