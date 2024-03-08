<?php
global $ae_post_factory, $user_ID;
$post_object = $ae_post_factory->get('ae_message');
$current = $post_object->convert($post);
?>

<li class="clearfix conversation-item">
    <div class="inner <?php echo isset($current->unread_class) ? $current->unread_class : ''; ?>">
        <a href="<?php echo isset($current->permalink) ? $current->permalink : ''; ?>" class="link"></a>
        <div class="img-avatar">
            <?php echo isset($current->author_avatar) ? $current->author_avatar : ''; ?>
        </div>
        <div class="conversation-text">
            <span class="latest-reply"> <?php echo isset($current->latest_reply_text) ? $current->latest_reply_text : ''; ?></span>
            <span class="latest-reply-time"><?php echo isset($current->latest_reply_time) ? $current->latest_reply_time: ''; ?></span>
        </div>
    </div>
</li>
