/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {
    /* Primary color */
    wp.customize('diplomat_primary_color', function(value) {
        value.bind(function(newval) {
            // Css rule for background
            window.addRule(
                '.diplomat .btn-diplomat, .diplomat .bg-customize',
                'background:' + newval
            );
            window.addRule(
                '.block-intro-how .text-information .icon',
                'background: ' + window.convertHex(newval, 70)
            );

            // Css rule for color
            window.addRule(
                '.diplomat .color-customize, .block-intro-how .steps .number-steps',
                'color:' + newval
            );

            // CSS rule for border-color
            window.addRule(
                '.block-intro-how .steps .number-steps, .block-intro-how .steps:before, .block-intro-how .text-information .steps-line',
                'border-color:' + newval
            );

        });
    });

    /* Block What */
    wp.customize('mje_diplomat_what_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-what').find('.name').html(newval);
        });
    });

    wp.customize('mje_diplomat_what_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-what').find('.text-content').html(newval);
        });
    });

    /* Homepage List title */
    wp.customize('mje_other_title_service', function(value) {
        value.bind(function(newval) {
            $('.block-items').find('h6').text(newval);
        });
    });

    wp.customize('mje_other_title_category', function(value) {
        value.bind(function(newval) {
            $('.block-categories').find('h6').text(newval);
        });
    });

    /* Block Why *
     *************/
    wp.customize('mje_diplomat_why_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why').find('h6').text(newval);
        });
    });
    // Item 1
    wp.customize('mje_diplomat_why_item_1_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-why .why-item-1 img', newval, ae_globals.skin_assets_path + "/img/why-icon-1.png");
        });
    });

    wp.customize('mje_diplomat_why_item_1_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-1').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_why_item_1_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-1').find('.content').text(newval);
        });
    });

    // Item 2
    wp.customize('mje_diplomat_why_item_2_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-why .why-item-2 img', newval, ae_globals.skin_assets_path + "/img/why-icon-2.png");
        });
    });

    wp.customize('mje_diplomat_why_item_2_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-2').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_why_item_2_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-2').find('.content').text(newval);
        });
    });

    // Item 3
    wp.customize('mje_diplomat_why_item_3_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-why .why-item-3 img', newval, ae_globals.skin_assets_path + "/img/why-icon-3.png");
        });
    });

    wp.customize('mje_diplomat_why_item_3_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-3').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_why_item_3_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-why .why-item-3').find('.content').text(newval);
        });
    });

    /* Block How *
     *************/
    wp.customize('mje_diplomat_how_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how').find('h6').text(newval);
        });
    });
    // Item 1
    wp.customize('mje_diplomat_how_item_1_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-how .how-item-1 img', newval, ae_globals.skin_assets_path + "/img/how-icon-1.png");
        });
    });

    wp.customize('mje_diplomat_how_item_1_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-1').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_how_item_1_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-1').find('.content').text(newval);
        });
    });

    // Item 2
    wp.customize('mje_diplomat_how_item_2_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-how .how-item-2 img', newval, ae_globals.skin_assets_path + "/img/how-icon-2.png");
        });
    });

    wp.customize('mje_diplomat_how_item_2_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-2').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_how_item_2_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-2').find('.content').text(newval);
        });
    });

    // Item 3
    wp.customize('mje_diplomat_how_item_3_img', function(value) {
        value.bind(function(newval) {
            window.onChangeImage('.block-intro-how .how-item-3 img', newval, ae_globals.skin_assets_path + "/img/how-icon-3.png");
        });
    });

    wp.customize('mje_diplomat_how_item_3_title', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-3').find('.title').text(newval);
        });
    });

    wp.customize('mje_diplomat_how_item_3_desc', function(value) {
        value.bind(function(newval) {
            $('.block-intro-how .how-item-3').find('.content').text(newval);
        });
    });

    /* Block footer */
    wp.customize('mje_diplomat_footer_img', function(value) {
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
                        $('.diplomat .block-login').css({
                            'background': 'url('+ res.data.full[0] +') no-repeat center center',
                            'background-size': 'cover'
                        })
                    } else {
                        $('.diplomat .block-login').css({
                            'background': 'url('+ ae_globals.skin_assets_path + '/img/bg-login.jpg' +') no-repeat center center',
                            'background-size': 'cover'
                        })
                    }

                    $( 'body' ).removeClass( 'wp-customizer-unloading' );
                }
            });
        });
    });

    wp.customize('mje_diplomat_login_footer_heading_title', function(value) {
        value.bind(function(newval) {
            $('.block-login .logged-in').find('.main-title').text(newval);
        });
    });

    wp.customize('mje_diplomat_login_footer_sub_title', function(value) {
        value.bind(function(newval) {
            $('.block-login .logged-in').find('.sub-title').text(newval);
        });
    });

    wp.customize('mje_diplomat_login_footer_button_text', function(value) {
        value.bind(function(newval) {
            $('.block-login .logged-in').find('.btn-link-site .text').text(newval);
        });
    });




} )( jQuery );
