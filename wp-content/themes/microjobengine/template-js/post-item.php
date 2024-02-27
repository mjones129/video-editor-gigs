<script type="text/template" id="ae-post-loop">
    <div class="image-avatar col-lg-4 col-md-4 col-sm-5 col-xs-12 image-post">
        <a href="{{= permalink }}">
            <img src="{{= the_post_thumbnail }}" alt="" class="img-responsive">
        </a>
    </div>
    <div class="info-items col-lg-8 col-md-8 col-sm-7 col-xs-12 article-post">
        <p class="author-post"><?php _e( 'Written by', 'enginethemes'); ?> {{= author_name }}</p>
        <p class="date-post">{{= post_human_time }}</p>
        <h2><a href="{{= permalink }}">{{= post_title }}</a></h2>
        <div class="group-function">
            {{= post_excerpt }}
            <a href="{{= permalink }}" class="more"><?php _e('Read more', 'enginethemes'); ?></a>
            <p class="total-comments">{{= comment_number }}</p>
        </div>
    </div>
</script>