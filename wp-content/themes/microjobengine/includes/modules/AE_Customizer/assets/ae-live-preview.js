/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {
    //Update site primary color
    var primary_color = ae_globals.skin_name + '_primary_color';
    var header_color = ae_globals.skin_name + '_header_color';
    var footer_color = ae_globals.skin_name + '_footer_color';
    wp.customize( primary_color, function( value ) {
        value.bind( function( newval ) {
            /**
             * Shadow
             */
            $('.search-form .btn-search, #et-header .link-post-services .plus-circle, .btn-button, .btn-submit, .link-post-job .plus-circle, .form-delivery-order .attachment-image .add-img i, .outer-conversation .attachment-image .add-img i').css('box-shadow', '1px 3px 9px ' + convertHex(newval, 70));

            $('.progress-bar ul li.active span').css('box-shadow',  '2px 5px 30px ' + convertHex(newval, 70));

            $('.paginations-wrapper .current').css('box-shadow', '1px 5px 11px ' + convertHex(newval, 70));

            /**
             * Background
             */
            $('.btn-submit, .modal-header, #et-header .list-message .list-message-box-header, #et-header .list-message .list-message-box-header, .et-dropdown .et-dropdown-login li:first-child, #et-header .link-post-services .plus-circle, .search-form .btn-search, .btn-post .cirlce-plus, .progress-bar .progress-bar-success, .post-job .add-more a .icon-plus, .link-post-job .plus-circle, .profile .block-billing ul li #billing_country .chosen-container .chosen-results li.highlighted, .withdraw #bankAccountForm .chosen-container .chosen-results li.highlighted, .profile .location .chosen-container .chosen-results li.highlighted, .profile .choose-location .chosen-container .chosen-results li.highlighted,.form-delivery-order .attachment-image .add-img i, .outer-conversation .attachment-image .add-img i, .mjob_conversation_detail_page .private-message .conversation-text, #content .carousel-indicators li.active, .mCS-minimal.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .mCS-minimal.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar, .custom-order-box .btn-send-offer').css('background', newval);

            /**
             * Color
             */
            $('#et-header .link-account ul li .open-signup-modal, #et-header .list-message .list-message-box-footer a, .fa-star, .fa-star-half-o, .block-items ul li .inner .price, #footer .et-pull-bottom a, .block-intro .load-more a, .not-member a, #accordion > li.active > .link > a, .not-found-sub-text .new-search-link, .not-found-sub-text a, .accordion li.open i, .accordion a.active, .block-items-detail .items-private .mjob-cat .mjob-breadcrumb .child, .block-items-detail .items-private .time-post span, .block-items-detail .package-statistic .price, .block-items-detail .list-extra li .package-price, .mjob-order-info .price, .mjob-order-info .total-price, .progress-bar ul li.active span, .package .chose-package .price, .count_down, .revenues ul li .currency, .currency-balance .price-balance, .dashboard .information-items-detail .tabs-information .view-all a, .list-job ul li .info-items .price, .list-order ul li .author a, .post-job .chosen-container .chosen-results li.highlighted, .block-items-detail .personal-profile .link-personal ul li .profile-link, .block-items-detail .personal-profile .link-personal ul li .profile-link i, .mjob-single-order-page .functions-items .date, .mjob_conversation_detail_page .message-time, .mjob-admin-dispute-form .text, .mjob-single-order-page .btn-dispute, .compose-conversation .compose .send-message, .mjob-dispute-form .compose .add-img i, .conversation-date, .compose-conversation .gallery_container_single_conversation .add-img i, .compose-conversation .carousel_single_conversation-image-list li a, .dashboard .information-items-detail .nav-tabs > li.active > a, .paginations-wrapper .current, .withdraw .payment-method .link-change-payment a, .load-more-post, .accordion .link a.active, .request-secure-code, .text-choosen a, .user-conversation a, .changelog-item .changelog-text a, .countdown, .mjob_conversation_detail_page .guest-message .conversation-text li a, .list-file li a, .nav-tabs > li.active > a, .custom-order-box .btn-decline, .custom-order-box .budget span, .mjob_conversation_detail_page .guest-message .conversation-text .budget span, .content-custom-order .date span, .content-custom-order .block-text .budget span, .content-custom-order .block-text .list-attach li a, .content-custom-order .block-text .budget span, .custom-order-box .more a, .custom-order-link a, .info-payment-method .sub-title').css('color', newval);

            addRule('.compose-conversation .input-compose input::-webkit-input-placeholder', 'color: ' + newval);
            addRule('.compose-conversation .input-compose input:-moz-placeholder', 'color: ' + newval);
            addRule('.compose-conversation .input-compose input:-ms-input-placeholder', 'color: ' + newval);
            addRule('.compose-conversation .input-compose input::-moz-placeholder', 'color: ' + newval);

            /**
             * Border color
             */
            $('.block-hot-items ul li .avatar img, .submenu,.mjob-single-order-page .btn-dispute, .mjob_conversation_detail_page .conversation-form .line, .custom-order-box .btn-decline').css( 'borderColor', newval );

            /**
             * Border top color
             */
            $('.compose-conversation, .dashboard .information-items-detail .nav-tabs > li.active > a, .mjob-dispute-form .compose, .nav-tabs > li.active > a').css( 'borderTopColor', newval );

            // Add style for pseudo
            addRule('.line-distance:after, .hvr-sweep-to-left:before, .profile .text-content:hover:before, .et-dropdown .et-dropdown-login li:hover',
                'background: ' + newval);

            addRule('.line-distance:before',
                'background: ' + convertHex(newval, 70));

            addRule(' .form-group .checkbox input[type="checkbox"]:not(:checked) + span:after, .form-group .checkbox input[type="checkbox"]:checked + span:after, .attachment-image ul li input[type="radio"]:not(:checked):after, .attachment-image ul li input[type="radio"]:checked:after, .form-group .checkbox input[type="radio"]:not(:checked) + span:after, .form-group .checkbox input[type="radio"]:checked + span:after, #et-header .link-post-services a:hover, .block-items h2 a:hover, .open-forgot-modal:hover, .btn-post:hover .cirlce-plus i, .accordion .link:hover i, .submenu a:hover, .post-job .chosen-container .chosen-results li:hover, .list-order li:hover a, .list-job li:hover a, #display_name .text-content:hover:before, .profile .block-billing ul li .text-content:hover:before, .profile .block-statistic ul li a:hover, .paginations-wrapper .page-numbers:hover, .list-job ul li .info-items a:hover, .mjob-single-order-page .order-detail-price .price-items',
                'color: ' + newval);

            addRule('.et-dropdown .et-dropdown-login:before, #et-header .list-message .list-message-box-header:before',
                'border-bottom-color: ' + newval);

            addRule('.et-form input[type="text"]:focus, .form-control:focus, .form-group .checkbox input[type="checkbox"]:not(:checked) + span:before, .form-group .checkbox input[type="checkbox"]:checked + span:before, .post-job li:hover, .attachment-image ul li input[type="radio"]:not(:checked):before, .attachment-image ul li input[type="radio"]:checked:before, .form-group .checkbox input[type="radio"]:not(:checked) + span:before, .form-group .checkbox input[type="radio"]:checked + span:before',
                'border-color: ' + newval);
        } );
    } );

    //Update site header color
    wp.customize( header_color, function( value ) {
        value.bind( function( newval ) {
            $('#et-header .et-pull-top').css('background', newval );

            // Color

        } );
    } );

    //Update site footer color
    wp.customize( footer_color, function( value ) {
        value.bind( function( newval ) {
            $('#footer').css('background', newval );
        } );
    } );

    // Update site logo
    wp.customize('site_logo', function(value) {
        value.bind(function(newval) {
            $('#logo-site a img').remove();
            $('#logo-site a').append('<img src="" />');
            onChangeImage('#logo-site img', newval, "");
        });
    });

    // Live preview for copyright
    wp.customize('site_copyright', function(value) {
       value.bind(function(newval) {
           $('.et-pull-bottom .site-copyright').html(newval);
       })
    });

    // Update search background image
    wp.customize('search_background', function(value) {
        value.bind(function(newval) {
            onChangeImage('.background-image img', newval, "");
        });
    });

    // Update post job banner
    wp.customize('post_job_banner', function(value) {
        value.bind(function(newval) {
            $('.banner .header-images img').remove();
            $('.banner .header-images').append('<img src="" />');
            onChangeImage('.banner .header-images img', newval, "");
        });
    });

    // Update footer background image
    wp.customize('footer_background', function(value) {
        value.bind(function(newval) {
            $.ajax({
                type: 'POST',
                url: ae_globals.ajaxURL,
                data: {
                    action: 'ae_customize_get_attachment_data',
                    attachment_id: newval,
                },
                beforeSend: function() {
                    $( 'body' ).addClass( 'wp-customizer-unloading' );
                },
                success: function(res) {
                    if(res.data != false) {
                        $('.block-intro').css({
                            'background': 'url('+ res.data.full[0] +') no-repeat center center',
                            'background-size': 'cover'
                        })
                    } else {
                        $('.block-intro').css({
                            'background': 'url()',
                        })
                    }

                    $( 'body' ).removeClass( 'wp-customizer-unloading' );
                }
            });
        });
    });

    // Live preview for homepage search title and sub title
    wp.customize('home_heading_title', function(value) {
       value.bind(function(newval) {
           $('.search-form').find('h1').text(newval);
       })
    });

    wp.customize('home_sub_title', function(value) {
        value.bind(function(newval) {
            $('.search-form').find('h4').text(newval);
        })
    });

    // Live preview for block About
    wp.customize('about_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro').find('.block-title').text(newval);
        })
    });

    wp.customize('about_link', function(value) {
        value.bind(function(newval) {
            $('.block-intro').find('.load-more a').attr('href', newval);
        })
    });

    // Live preview for block About 1
    wp.customize('about_col_1_icon', function(value) {
        value.bind(function(newval) {
            onChangeImage('.block-intro-1 .icon-article img', newval, ae_globals.assetImg + "icon-intro-1.png");
        });
    });

    wp.customize('about_col_1_title', function(value) {
       value.bind(function(newval) {
          $('.block-intro-1 .text-article').find('.title').text(newval);
       });
    });

    wp.customize('about_col_1_link', function(value) {
        value.bind(function(newval) {
            $('.block-intro-1 .text-article').find('.title').attr('href', newval);
        });
    });

    wp.customize('about_col_1_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-1 .text-article').find('p').text(newval);
        });
    });

    // Live preview for block About 2
    wp.customize('about_col_2_icon', function(value) {
        value.bind(function(newval) {
            onChangeImage('.block-intro-2 .icon-article img', newval, ae_globals.assetImg + "icon-intro-2.png");
        });
    });

    wp.customize('about_col_2_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-2 .text-article').find('.title').text(newval);
        });
    });

    wp.customize('about_col_2_link', function(value) {
        value.bind(function(newval) {
            $('.block-intro-2 .text-article').find('.title').attr('href', newval);
        });
    });

    wp.customize('about_col_2_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-2 .text-article').find('p').text(newval);
        });
    });

    // Live preview for block About 3
    wp.customize('about_col_3_icon', function(value) {
        value.bind(function(newval) {
            onChangeImage('.block-intro-3 .icon-article img', newval, ae_globals.assetImg + "icon-intro-3.png");
        });
    });

    wp.customize('about_col_3_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-3 .text-article').find('.title').text(newval);
        });
    });

    wp.customize('about_col_3_link', function(value) {
        value.bind(function(newval) {
            $('.block-intro-3 .text-article').find('.title').attr('href', newval);
        });
    });

    wp.customize('about_col_3_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-3 .text-article').find('p').text(newval);
        });
    });

    // Live preview for home categories and services title
    wp.customize('mje_other_title_category', function(value) {
        value.bind(function(newval) {
            $('.block-hot-items .block-title').text(newval);
        })
    });
    wp.customize('mje_other_title_service', function(value) {
        value.bind(function(newval) {
            $('.block-items .block-title').text(newval);
        })
    });

    // Live preview for posting banner
    wp.customize('post_job_title', function(value) {
        value.bind(function(newval) {
            console.log(newval);
            $('.banner-title').text(newval);
        })
    });

    // Disable banner
    wp.customize('mje_disable_banner', function (value) {
        value.bind(function (newval) {
           if (newval === true) {
             $('.banner').fadeOut();
           } else {
             $('.banner').fadeIn();
           }
        })
    });

    // call ajax to get attachment data
    window.onChangeImage = function (el, attachment_id, replace_url) {
        $.ajax({
            type: 'POST',
            url: ae_globals.ajaxURL,
            data: {
                action: 'ae_customize_get_attachment_data',
                attachment_id: attachment_id,
            },
            beforeSend: function() {
                $( 'body' ).addClass( 'wp-customizer-unloading' );
            },
            success: function(res) {
                if(res.data != false) {
                    $(el).attr('src', res.data.full[0]);
                } else {
                    $(el).attr('src', replace_url);
                }

                $( 'body' ).removeClass( 'wp-customizer-unloading' );
            }
        });
    }

    /**
     * Convert hex to rgba
     * @param hex
     * @param opacity
     * @returns {string|*}
     */
    window.convertHex = function (hex,opacity){
        hex = hex.replace('#','');
        r = parseInt(hex.substring(0,2), 16);
        g = parseInt(hex.substring(2,4), 16);
        b = parseInt(hex.substring(4,6), 16);

        result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
        return result;
    }

    window.addRule = function (selector, styles, sheet) {
        var css = selector + '{' + styles + '}',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
    };
} )( jQuery );
