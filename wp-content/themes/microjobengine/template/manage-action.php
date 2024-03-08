<?php
global $user_ID;
if( is_super_admin() || $user_ID == $current->post_author ):
    $edit_link = $current->permalink.'?action=edit';
    if( $current->post_status == 'pending'){
        $edit_link = $current->permalink.'&action=edit';
    }
?>
<div class="actions">
    <input type="hidden" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    <?php if($current->post_status == 'pending'): ?>
        <ul>
            <?php if(!is_super_admin() ): ?>
                <li><a href="<?php echo $edit_link; ?>" target="_blank"  data-toggle="tooltip" data-placement="top" title="<?php _e('Edit', 'enginethemes') ?>" class=""><i class="fa fa-pencil"></i></a></li>
                <li><a href="#" data-action="delete" data-toggle="tooltip" data-placement="top" title="<?php _e('Delete', 'enginethemes') ?>"class="action"><i class="fa fa-trash-o"></i></a></li>
            <?php else: ?>
                <li><a href="#" data-action="approve" data-toggle="tooltip" data-placement="top" title="<?php _e('Approve', 'enginethemes') ?>" class="action"><i class="fa fa-check"></i></a></li>
                <li><a href="#" data-action="reject" data-toggle="tooltip" data-placement="top" title="<?php _e('Reject', 'enginethemes') ?>" class="action"><i class="fa fa-times"></i></a></li>
                <li><a href="<?php echo $edit_link ?>" target="_blank" data-action="edit" data-toggle="tooltip" data-placement="top" title="<?php _e('Edit', 'enginethemes') ?>" class=""><i class="fa fa-pencil"></i></a></li>
            <?php endif; ?>
        </ul>
    <?php elseif($current->post_status == 'publish' && !is_search()): ?>
    <ul>
        <?php if(is_super_admin() ): ?>
            <li><a href="<?php echo $edit_link ?>" target="_blank" title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
            <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
            <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
        <?php else: ?>
            <?php echo MJE_MJob_Action::showEditLink( $current ); ?>
            <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
            <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
        <?php endif; ?>
    </ul>
    <?php elseif($current->post_status == 'archive' && !is_search()): ?>
    <ul>
        <li><a href="<?php echo et_get_page_link('post-service');?>?id=<?php echo $current->ID ?>" title="<?php _e('Renew', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-refresh"></i></a></li>
        <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
    </ul>
    <?php elseif($current->post_status == 'reject' && !is_search()): ?>
    <ul>
        <li><a href="<?php echo $edit_link ?>" target="_blank" title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
        <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
    </ul>
    <?php elseif($current->post_status == 'pause' && !is_search()): ?>
        <ul>
            <?php if(is_super_admin() ): ?>
                <li><a href="#" data-action="unpause" title="<?php _e('Unpause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top"  class="action"><i class="fa fa-play"></i></a></li>
                <li><a href="<?php echo $edit_link ?>" target="_blank" data-action="edit" title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></i></a></li>
                <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
            <?php else: ?>
                <?php echo MJE_MJob_Action::showEditLink( $current ); ?>
                <li><a href="#" data-action="unpause" title="<?php _e('Unpause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top"  class="action"><i class="fa fa-play"></i></a></li>
                <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
            <?php endif; ?>
        </ul>
<?php elseif($current->post_status == 'draft' && !is_search()): ?>
    <ul>
        <li><a href="<?php echo et_get_page_link('post-service');?>?id=<?php echo $current->ID ?>" title="<?php _e('Submit', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-arrow-up"></i></a></li>
        <li><a href="#" data-action="delete" title="<?php _e('Delete', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-trash-o"></i></a></li>
    </ul>
 <?php elseif(!is_search()) : ?>
        <ul>
            <?php if(is_super_admin() ): ?>
                <li><a href="<?php echo $edit_link ?>" target="_blank"  title="<?php _e('Edit', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class=""><i class="fa fa-pencil"></i></a></li>
                <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
                <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
            <?php else: ?>
                <?php echo MJE_MJob_Action::showEditLink( $current ); ?>
                <li><a href="#" data-action="pause" title="<?php _e('Pause', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-pause"></i></a></li>
                <li><a href="#" data-action="archive" title="<?php _e('Archive', 'enginethemes') ?>" data-toggle="tooltip" data-placement="top" class="action"><i class="fa fa-archive"></i></a></li>
            <?php endif; ?>
        </ul>
<?php endif; ?>
</div>
<?php
endif; ?>