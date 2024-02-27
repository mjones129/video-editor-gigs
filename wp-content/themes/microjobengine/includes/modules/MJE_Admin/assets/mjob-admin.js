(function($, Views, Models, Collections, AE) {
    $(document).ready(function() {
        // Init option sync
        var options = new Models.Options();
        $('form').validate();
        if ($('.wrapper-welcome').length > 0) {
            var options_view = new Views.Options({
                el: '.wrapper-welcome',
                model: options
            });
        }

        Views.mJobAdmin = Backbone.View.extend({
            el: '.content-admin-setting',
            events: {
                'keyup #mjob_min_price': 'onChangeMinPrice',
                'keyup #mjob_max_price': 'onChangeMaxPrice',
                'click .btn-toggle-help': 'toggleHelp',
                'click .toggle-desc' : 'toggleContent'
            },
            initialize: function() {
                this.$priceModeSwitch = $('input[name="custom_price_mode"]');
                this.$fixPrice = $('#field-fixed_price');
                this.$customPrice = $('#field-custom_price');

                //Model option
                this.option = new Models.Options();

                //Toggle price fields
                if(this.$priceModeSwitch.val() == '1') {
                    this.$fixPrice.hide();
                } else {
                    this.$customPrice.hide();
                }

                //payment type init check
                this.initShowHidePaymentPlan();
                $packageList = $('#group-list-package-wrapper');
                if($('input[name="disable_plan"]').val() == '1') {
                    $packageList.find('.group-fields').append('<p class="disable-plan-notice">'+ ae_globals.disable_plan_notice +'</p>');
                    $('#control-payment_package').hide();
                } else {
                    $packageList.find('.group-fields').find('.disable-plan-notice').remove();
                    $('#control-payment_package').show();
                }

                //Listen on the switch changing
                AE.pubsub.on('ae:option:switch:change', this.switchChange, this);
                //Listen on the text field changing
                AE.pubsub.on('ae:option:text:change', this.textChange, this);

                AE.pubsub.on('ae:option:switch:change', this.initShowHidePaymentPlan);

                AE.pubsub.on('ae:option:field:toggle', this.onToggle, this);

                AE.pubsub.on('ae:on:after:fetch', this.onAfterFetch, this);
            },
            toggleHelp: function(e){
                e.preventDefault();
                if($('.content-help-row').length > 0) {
                    $('.content-help-row').toggle();
                } else {
                    $('.content-help-row').remove();
                    var helpText = $(e.currentTarget).next('.cont-template-help').html();
                    var parent = $(e.currentTarget).parents('.group-title');
                    parent.append('<div class="content-help-row">'+ helpText +'</div>');
                }
            },
            toggleContent: function(e) {
                e.preventDefault();
                var $target = $(e.currentTarget);
                var icon = $target.find('i');
                // Get DOM of field content and do toggle
                var toggleContent = $target.parent('.field-desc').next('.field-content');
                toggleContent.toggle();

                // Change icon
                if(toggleContent.is(':hidden')) {
                    icon.attr('class', 'fa fa-plus-square');
                } else {
                    icon.attr('class', 'fa fa-minus-square');
                }
            },
            getMaxPrice: function() {
                var maxPrice = $('#mjob_max_price').val();
                if(maxPrice == "") {
                    maxPrice = 0;
                } else {
                    maxPrice = parseInt(maxPrice);
                }
                return maxPrice;
            },
            getMinPrice: function() {
                //Min price validate
                var minPrice = $('#mjob_min_price').val();
                if(minPrice == "") {
                    minPrice = 0;
                } else {
                    minPrice = parseInt(minPrice);
                }
                return minPrice;
            },
            validateMinPrice: function() {
                var maxPrice = this.getMaxPrice();
                //Validator method
                $.validator.addMethod("pMax", $.validator.methods.max, mJobAdmin.min_price_error);
                $.validator.addClassRules('min_price_validate', { pMax: maxPrice - 1 });
            },
            validateMaxPrice: function() {
                var minPrice = this.getMinPrice();
                //Validator method
                $.validator.addMethod("pMin", $.validator.methods.min, mJobAdmin.max_price_error);
                $.validator.addClassRules('max_price_validate', { pMin: minPrice + 1 });
            },
            onChangeMinPrice: function() {
                this.validateMinPrice();
                this.validateMaxPrice();
            },
            onChangeMaxPrice: function() {
                this.validateMaxPrice();
                this.validateMinPrice();
            },
            switchChange: function(data) {
                var view = this;
                if(data.name == 'custom_price_mode') {
                    if(data.type == 'disable') {
                        view.$fixPrice.fadeIn();
                        view.$customPrice.hide();
                    }

                    if(data.type == 'enable') {
                        view.$customPrice.fadeIn();
                        view.$fixPrice.hide();
                    }
                }
            },
            textChange: function(data) {
                var view = this;
                if(data.name == 'mjob_max_price') {
                    view.option.set('name', 'mjob_min_price');
                    view.option.set('value', view.getMinPrice());
                    view.option.save();
                } else if(data.name == 'mjob_min_price') {
                    view.option.set('name', 'mjob_max_price');
                    view.option.set('value', view.getMaxPrice());
                    view.option.save();
                }
            },
            initShowHidePaymentPlan: function(data) {
                if( typeof data !== 'undefined' && data.name == 'disable_plan') {
                    $packageList = $('#group-list-package-wrapper');
                    if($('input[name="disable_plan"]').val() == '1') {
                        $packageList.find('.group-fields').append('<p class="disable-plan-notice">'+ ae_globals.disable_plan_notice +'</p>');
                        $('#control-payment_package').hide();
                    } else {
                        $packageList.find('.group-fields').find('.disable-plan-notice').remove();
                        $('#control-payment_package').show();
                    }
                }
            },
            onToggle: function(target, form) {
                if(form.find('.toggle').is(':hidden')) {
                    //Hide
                    target.find('.fa').removeClass('fa-minus-square');
                    target.find('.fa').addClass('fa-plus-square');
                } else {
                    //Show
                    target.find('.fa').removeClass('fa-plus-square');
                    target.find('.fa').addClass('fa-minus-square');
                }
            },
            onAfterFetch: function(result, res, $target) {
                if($target.hasClass('orderby')) {
                    $('.sort-link .orderby i').attr('class', 'fa fa-sort');
                    order = $target.attr('data-order');
                    if(order == 'desc') {
                        $target.attr('data-order', 'asc');
                        $target.find('i').remove();
                        $target.append('<i class="fa fa-sort-asc"></i>');
                    } else if(order == 'asc') {
                        $target.attr('data-order', 'desc');
                        $target.find('i').remove();
                        $target.append('<i class="fa fa-sort-desc"></i>');
                    }
                }
            }
        });

        new Views.mJobAdmin();

        /**
         * PAGE CHOOSE SKINS
         */
        Views.ChooseSkins = Backbone.View.extend({
            el: '#select-skins',
            events: {
                'click .select': 'selectSkin',
                'click .preview': 'previewSkin'
            },
            initialize: function() {
                this.optionModel = new Models.Options();
            },
            selectSkin: function(e) {
                e.preventDefault();
                var $target = $(e.currentTarget);
                var skinName = $target.attr('data-name');
                if(skinName == '') {
                    skinName = 'default';
                }

                this.optionModel.set('name', 'mjob_skin_name');
                this.optionModel.set('value', skinName);

                this.optionModel.save('', '', {
                    beforeSend: function() {

                    },
                    success: function(status, resp, xhr) {
                        if(resp.success) {
                            window.location.reload();
                        }
                    }
                })
            },
            previewSkin: function(e) {
                e.preventDefault();
                var $target = $(e.currentTarget);
                var previewImg = $target.attr('data-preview');
                var previewModal = new Views.PreviewSkinModal({
                    previewImg: previewImg
                });
                previewModal.open();
            }
        });

        /**
         * MODAL PREVIEW SKIN
         */
        Views.PreviewSkinModal = Views.Modal_Box.extend({
            el: '#preview-skin-modal',
            initialize: function(options) {
                this.options = _.extend(this, options);
            },
            open: function() {
                this.$el.find('.modal-body').html('');
                this.$el.find('.modal-body').append('<img style="width: 100%; height: auto" src="' + this.options.previewImg + '"/>');
                this.openModal();
            }
        });

        new Views.ChooseSkins();
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections, window.AE);