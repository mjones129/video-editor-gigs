<script type="text/template" id="conversation-item-loop">
    <div class="inner clearfix {{= unread_class }}">
        <div class="img-avatar">
            {{= author_avatar }}
        </div>
        <a href="{{= permalink }}">
            <div class="conversation-text">
                <p class="name-author">{{= author_name }}</p>
                <span class="latest-reply">
                    {{= latest_reply_text }}
                </span>
                <p class="latest-reply-time">{{= latest_reply_time }}</p>
            </div>
        </a>
    </div>
</script>