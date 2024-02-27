<?php
    global $post, $ae_post_factory, $user_ID;
    $post_object = $ae_post_factory->get('ae_message');
    $current = $post_object->convert($post);
?>
<li class="clearfix conversation-item">
    <div class="inner <?php echo isset($current->unread_class) ? $current->unread_class : ''; ?> clearfix">
        <div class="img-avatar">
            <?php echo $current->author_avatar; ?>
        </div>
        <a href="<?php echo isset($current->permalink) ? $current->permalink : ''; ?>">
            <div class="conversation-text">
                <p class="name-author"><?php echo isset($current->author_name) ? $current->author_name : ''; ?></p>
                    <span class="latest-reply">
                        <?php echo isset($current->latest_reply_text) ? $current->latest_reply_text : ''; ?>
                    </span>
                <p class="latest-reply-time"><?php echo isset($current->latest_reply_time) ? $current->latest_reply_time : ''; ?></p>
            </div>
        </a>
    </div>
</li>