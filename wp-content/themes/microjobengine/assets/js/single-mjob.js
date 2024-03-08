/**
 * Created by Jack Bui on 1/12/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {

        //define extra item
        var extraItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'extra-item',
            template: _.template($('#extra-item-loop').html()),
            events: _.extend({
                'click input[type="checkbox"]': 'checkBox'
            }, Views.PostItem.prototype.events),
            onItemBeforeRender: function () {
                // before render view
            },
            onItemRendered: function () {
                var view = this;
                view.$el.attr('data-id', view.model.get('ID'));
            },
            checkBox: function (e) {
                $target = $(e.currentTarget);
                AE.pubsub.trigger('mjob:extra:add', $target);
            }
        });
        /**
         * list view control mjob list
         */
        ListExtras = Views.ListPost.extend({
            tagName: 'ul',
            itemView: extraItem,
            itemClass: 'extra-item',
            initialize: function(options) {
                _.extend(this, options);
                Views.ListPost.prototype.initialize.call(this, options);
            },
        });
        /**
         * Orders
         *
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author JACK BUI
         */
        Views.SingleMjob = Backbone.View.extend({
            el: '.mjob-single-page',
            events: {
                'click .mjob-order-action': 'mJobOrderAction',
                'click .edit-mjob-action': 'openEditForm',
                'click .mjob-order-disable': 'disableNotification',
                'click .show-opening-message' : 'showLessOpeningMessage'
            },
            initialize: function (options) {
                var view = this;
                if( $('#mjob_single_data').length > 0 ){
                    data = JSON.parse($('#mjob_single_data').html());
                    this.model = new Models.Mjob(data)
                }
                else{
                    this.model = new Models.Mjob();
                }
                if( this.$el.find('.rate-it').length > 0 ) {
                    this.$el.find('.rate-it').raty({
                        readOnly: true,
                        half: true,
                        score: function () {
                            return view.model.get('rating_score');
                        },
                        hints: raty.hint
                    });
                }
                this.amount = 0;
                if(  parseFloat($('input[name="amount"]').val()) > 0 ){
                    this.amount = parseFloat($('input[name="amount"]').val());
                }
                if(  parseFloat($('input[name="amount_fee"]').val()) > 0 ){
                    this.amount_fee = parseFloat($('input[name="amount_fee"]').val());
                }
                this.initExtras();
                AE.pubsub.on('mjob:extra:add', this.onAddExtras, this);
                AE.pubsub.on('ae:after:edit:mjob', this.updateSingleContent, this);
                this.renderExtras();
                AE.pubsub.on('mjob:after:sync:extra', this.fetchExtraList, this);
                if( this.model.get('is_edit') ){
                    this.OpenEditMjob();
                }
            },
            fetchExtraList: function(result, res, jqXHR){
                var view = this;
                this.fetchExtras(result, res, jqXHR);

            },
            fetchExtras: function(result, res, jqXHR){
                var view = this;
                view.extraCollection.fetch({
                    data:{
                        query: {
                            post_parent: res.data.post_parent
                        },
                        page: 1,
                    },
                    beforeSend: function() {
                    },
                    success: function(result, res, xhr) {
                        $('.extra-container .no-extra').remove();
                    }
                });
            },
            renderExtras: function(){
                var view = this;
                $('.extra-container').each(function () {
                    if (typeof view.extraCollection == 'undefined') {
                        //Get public  collection
                        if ($('.extra_postdata').length > 0) {
                            var extra = JSON.parse($('.extra_postdata').html());
                            view.extraCollection = new Collections.Extras(extra);
                        } else {
                            view.extraCollection = new Collections.Extras();
                        }
                    }
                    /**
                     * init list blog view
                     */
                    view.listExtras = new ListExtras({
                        itemView: extraItem,
                        collection: view.extraCollection,
                        el: $(this).find('.mjob-list-extras'),
                        appendHtml: function(collectionView, itemView, index){
                            collectionView.$el.append(itemView.el);
                        }
                    });
                    view.listExtras.render();
                    /**
                     * init block control list blog
                     */
                    new Views.BlockControl({
                        collection: view.extraCollection,
                        el: $(this)
                    });
                });
                this.initExtras();
            },
            onAddExtras: function ($target) {
                var view = this,
                    price = $target.val();
                if( $target.prop('checked') ){
                    this.amount = parseFloat(this.amount) + parseFloat(price);
                    this.extra_ids.push($target.attr('data-id'));
                }
                else{
                    this.amount = parseFloat(this.amount) - parseFloat(price);
                    index = this.extra_ids.indexOf($target.attr('data-id'));
                    if( index != -1 ){
                        this.extra_ids.splice(index, 1);
                    }
                }
                if( this.amount < 0 ){
                    this.amount = 0;
                }
                view.updateAmount(this.amount);
                view.updateCheckBox($target);
            },
            updateAmount: function (amount) {
                var amount = AE.App.number_commission_for_buyer(amount);
                amount_text = AE.App.mJobPriceFormat(amount);
                $('.mjob-price').html(amount_text);

            },
            updateCheckBox: function($target){
                var view = this;
                view.$el.find('.extra-item').each(function(){
                    if( $(this).attr('data-id') == $target.attr('data-id')){
                        if( !$target.prop('checked') ) {
                            $(this).find('input[type="checkbox"]').prop('checked', false);
                        }
                        else{
                            $(this).find('input[type="checkbox"]').prop('checked', true);
                        }
                    }
                });
            },
            mJobOrderAction: function(e){
                e.preventDefault();
                var view = this;

                // Check is user logged in
                if(typeof currentUser.data === "undefined" || currentUser.id == 0) {
                    // Open sign in modal
                    if(typeof this.signInModal === "undefined") {
                        this.signInModal = new Views.SignInModal();
                    }
                    this.signInModal.openModal();
                    AE.pubsub.trigger('mjob:open:signin:modal', this.signInModal);
                } else if(currentUser.data.register_status == "unconfirm") {
                    AE.pubsub.trigger('ae:notification', {
                        notice_type: "error",
                        msg: ae_globals.pending_account_error_txt
                    });
                } else {
                    var permalinkStructure = ae_globals.permalink_structure;
                    url = ae_globals.order_link;

                    if(permalinkStructure == "") {
                        url += '&pid='+this.model.get('ID');
                    } else {
                        url += '?pid='+this.model.get('ID');
                    }

                    $('.mjob-add-extra').find('input[type="checkbox"]').each(function(){
                        if($(this).prop('checked') ){
                            url += '&extras_ids[]='+$(this).attr('data-id');
                        }
                    });
                   window.location.href = url;
                }
            },
            initExtras: function(){
                var view = this;
                view.extra_ids = new Array();
                if( $('#mjob-extra-ids').length >0 ){
                    view.extra_ids = JSON.parse($('#mjob-extra-ids').html());
                    $('.extra-item').each(function(){
                        if( view.extra_ids.indexOf($(this).attr('data-id') ) != -1 ){
                            $(this).find('input[type="checkbox"]').prop('checked', true);
                        }
                    });
                }
            },
            openEditForm: function(e){
                e.preventDefault();
                this.OpenEditMjob();
            },
            OpenEditMjob: function(){
                var view = this;
                if(typeof view.editForm === 'undefined'){
                    view.editForm = new Views.EditMjob({model: this.model});
                }
                if(( view.model.get('post_author') == ae_globals.user_ID || ae_globals.is_admin )) {
                    view.editForm.render();
                }
            },
            showContent: function(){
                window.location=window.location;
				//$('.mjob-edit-content').fadeOut(500);
				//$('.mjob-single-content').fadeIn(500);
            },
            updateSingleContent: function(result, res, jqXHR){
                var data = res.data;
                var view = this;
                $('.mjob-single-header h2').find('.rendered-text').text(data.post_title);
                $('.mjob-single-description').find('.post-content').html(data.post_content);
                $('.mjob-cat-breadcrumb').html(data.tax_input.mjob_category[0].name);
                $('.mjob-modified-day').html(data.modified_date);
                $('.time-delivery-label').html(data.time_delivery);

                // Update price
                $('.mjob-single-stat .price').html(data.et_budget_text);
                $('button.mjob-order-action .mjob-price').html(data.et_budget_text);

                var html = '';
                var active = 'active'
                var listhtml = '';
                for( var i = 0; i< res.data.et_carousel_urls.length; i++){
                    listhtml += '<li data-target="#carousel-example-generic" data-slide-to="'+i+'" class="'+active+'"></li>';
                    html += '<div class="item '+active+'"> <img src="'+res.data.et_carousel_urls[i].slider_img_url+'" alt=""> </div>'
                    active = '';
                }
                $('.mjob-single-carousels').html(html);
                $('.mjob-carousel-indicators').html(listhtml);
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        action: 'mjob-get-breadcrumb-list',
                        term_id: data.tax_input.mjob_category[0].term_id
                    },
                    beforeSend: function () {
                    },
                    success: function (res) {
                        $('.mjob-cat-breadcrumb').html(res);
                    }
                });
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        action: 'mjob-get-skill-list',
                        ID: data.ID
                    },
                    beforeSend: function () {
                    },
                    success: function (res) {
                        $('.tags').html(res);
                    }
                });

                /**
                 * Add trigger when updated single mjob data
                 */
                AE.pubsub.trigger('mje:after:update:mjob:data', data);

                this.showContent();
            },
            disableNotification: function(e){
                e.preventDefault();
                AE.pubsub.trigger('ae:notification', {
                    msg: ae_globals.disableNotification,
                    notice_type: 'error'
                });
            },
            showLessOpeningMessage: function(e) {
                e.preventDefault();
                //Show more and show less opening message in mjob
                $('.content-opening-message').toggleClass('hide-content').toggleClass('gradient');

                if($('.content-opening-message').hasClass('hide-content')) {
                    $('.show-opening-message').text(ae_globals.show_bio_text);
                } else {
                    $('.show-opening-message').text(ae_globals.hide_bio_text);
                }
                // $('html, body').stop().animate({scrollTop: top - 300}, 1000, 'swing');
            }
        });
        /**
         * edit mjob
         *
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author JACK BUI
         */
        Views.EditMjob = Backbone.View.extend({
            el: '.mjob-edit-content',
            events:{
                'click .mjob-discard-action': 'discardMjob',
                'click .mjob-add-extra-btn': 'addExtras',
                'submit form.edit-mjob-form': 'submitPost',
                'click .mjob-img-wrapper' : 'showPreviewImage'
            },
            initialize: function() {
                this.initExtras();
                this.initValidator();
                this.blockUi    = new Views.BlockUi();
                //AE.pubsub.on('ae:carousel:after:remove', this.checkImage, this);
                AE.pubsub.on('ae:carousel:before:remove', this.beforeRemoveImage, this);
                AE.pubsub.on('ae:carousel:after:remove', this.afterRemoveImage, this);
                AE.pubsub.on('Upload:Success', this.uploadSuccess, this);
                AE.pubsub.on('ae:carousel:cannot:remove', this.canNotRemove, this);
            },
            render: function() {
                var view = this;
                // show edit form
                $('.mjob-single-content').fadeOut(500);
                view.$el.fadeIn(500);
                view.$el.find('.edit-mjob-form').fadeIn(500);
                $('form.edit-mjob-form')['0'].reset();
                $('.skills-list').html('');
                view.$el.find('.carousel-gallery img').attr('src', ae_globals.mJobDefaultGalleryImage);
                view.setupFields();
            },
            uploadSuccess: function(res){
                var view = this;

                if($('.carousel-image-list .image-item').length == 1) {
                    $('.carousel-image-list').find('.image-item:last-child input').prop('checked', true);
                }

                if( typeof res.data.full !== 'undefined' ) {
                    view.changeImage(res.data.mjob_detail_slider[0], res.data.attach_id);
                    if( view.$el.find('input[name="et_carousels"]').length > 0){
                        view.$el.find('.carousel-gallery input, .carousel-gallery label').remove();
                    };
                }
                /**
                 * update model from input, textarea, select
                 */
                if (view.mjobModel.get('uploadingCarousel')) return false;
                /**
                 * update model data
                 */
                view.$el.find('.input-item, .wp-editor-area').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                view.$el.find('.tax-item').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                // trigger method before SubmitPost
                /**
                 * update input check box to model
                 */
                view.$el.find('input[type=checkbox]').each(function() {
                    var name = $(this).attr('name');
                    view.model.set(name, []);
                });
                view.$el.find('input[type=checkbox]:checked').each(function() {
                    var name = $(this).attr('name');
                    if (name == "et_claimable_check") return false;
                    if (typeof temp[name] !== 'object') {
                        temp[name] = new Array();
                    }
                    temp[name].push($(this).val());
                    view.mjobModel.set(name, temp[name]);
                });
                /**
                 * update input radio to model
                 */
                view.$el.find('input[type=radio]:checked').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                view.mjobModel.set('skill', view.skill_control.model.get('skill'));
                view.mjobModel.save('', '', {
                    beforeSend: function() {
                        //view.blockUi.block($target.find('.btn-save'));
                        //view.loading();
                    },
                    success: function(result, res, jqXHR) {

                        if (res.success) {
                            if( typeof view.model.get('ID') !== 'undefined' ) {
                                view.$el.find('input[name="post_parent"]').val(view.model.get('ID'));
                            }
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.uploadSuccess,
                                notice_type: 'success'
                            });
                            var html = '';
                            var active = 'active'
                            var listhtml = '';
                            for( var i = 0; i< res.data.et_carousel_urls.length; i++){
                                listhtml += '<li data-target="#carousel-example-generic" data-slide-to="'+i+'" class="'+active+'"></li>';
                                html += '<div class="item '+active+'"> <img src="'+res.data.et_carousel_urls[i].guid+'" alt=""> </div>'
                                active = '';
                            }
                            $('.mjob-single-carousels').html(html);
                            $('.mjob-carousel-indicators').html(listhtml);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            beforeRemoveImage: function(model, li) {
                var view = this;

                var isChecked = li.find('input').is(':checked');
                var index = li.index();
                if(isChecked) {
                    if(index == 0) {
                        li.next().find('input').prop('checked', true);
                    } else {
                        li.prev().find('input').prop('checked', true);
                    }
                }

                if($('.image-list').find('li').length <= 1 ){
                    this.changeImage(ae_globals.mJobDefaultGalleryImage, '');
                    if( view.$el.find('input[name="et_carousels"]').length == 0){
                        view.$el.find('.carousel-gallery').append('<input type="hidden" name="et_carousels" value="" />');
                        $('.upload-description').removeClass('hide');
                    }
                } else {
                    if(index == 0) {
                        var src = li.next().first().find('a').attr('data-full');
                        var attach_id = li.next().find('a').attr('data-id');
                        this.changeImage(src, attach_id);
                    } else {
                        var src = li.prev().first().find('a').attr('data-full');
                        var attach_id = li.prev().find('a').attr('data-id');
                        this.changeImage(src, attach_id);
                    }
                }
            },
            afterRemoveImage: function(model) {
              var view = this;

              // Update featured image
              var selectedImage = '';
              $('.carousel-image-list li').find('input[type="radio"]').each(function() {
                if($(this).prop('checked') == true) {
                  selectedImage = $(this).parents('li').attr('id');
                }
              });

              view.mjobModel.set('featured_image', selectedImage);
              view.mjobModel.set('_wpnonce', $('.post-service_nonce').val());
              view.mjobModel.save();
            },
            showPreviewImage: function(event){
                var view = this;
                event.preventDefault();
                var $target = $(event.currentTarget);

                // Select featured image
                var radioSelect = $target.parent('.img-gallery').next('input')
                radioSelect.prop('checked', true);

                var attach_id = $target.attr('data-id');
                view.changeImage($target.attr('data-full'), attach_id);
            },
            changeImage: function(src, attach_id){
                var view = this;
                if(typeof $('.mjob-replace-image').attr('data-delete') !== 'undefined' ){
                    attachID  = $('.mjob-replace-image').attr('data-delete');
                    $('#mjob-delete-' + attachID).click();
                }
                view.$el.find('.carousel-gallery img').attr('src', src);
                view.$el.find('.mjob-delete-image').attr('data-id', attach_id);
                view.$el.find('.mjob-replace-image').attr('data-id', attach_id);
            },
            setupFields: function(){
                var view = this;
                var data = {
                    action: 'mjob-get-mjob-infor',
                    ID: view.model.get('ID')
                };
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function () {
                      $('.mjob-edit-content').find('.loading').show();
                    },
                    success: function (res) {
                        if (res.success) {
                            // hide loading
                            $('.mjob-edit-content').find('.loading').hide();

                            // binding data
                            view.mjobModel = new Models.Mjob(res.data);
                            if(view.mjobModel.get('post_status') == 'reject'){
                                view.mjobModel.set('post_status', 'pending');
                            }
                            $('.form-group').find('input[type="text"],input[type="number"],input[type="hidden"], textarea,select').each(function() {
                                var $input = $(this);
                                if( $input.attr('name') != '_wpnonce' ) {
                                    $input.val(view.mjobModel.get($input.attr('name')));
                                    // trigger chosen update if is select
                                    if ($input.get(0).nodeName === "SELECT") $input.trigger('chosen:updated');
                                }
                            });

                            if (typeof view.carousels === 'undefined') {
                                view.carousels = new Views.Carousel({
                                    el: $('.gallery_container'),
                                    name_item:'et_carousel',
                                    uploaderID:'carousel',
                                    model: view.mjobModel,
                                    min_images: 0,
                                    filters: {
                                        resolution_limit: {
                                            min: { width: 768, height: 435 }
                                        }
                                    }
                                });
                            } else {
                                view.carousels.setModel(view.mjobModel);
                                view.carousels.setupView();
                            }
                            if(typeof view.skill_control === 'undefined') {
                                view.skill_control = new Views.Skill_Control({el : view.$el.find('.skill-control')});
                            }
                            view.skill_control.setModel(view.mjobModel);
                            view.reSetupSkills();
                            if( typeof view.mjobModel.get('mjob_extras') !== 'undefined' && view.mjobModel.get('mjob_extras').length > 0){
                                view.extrasCollection.reset(view.mjobModel.get('mjob_extras'));
                            }
                            if(view.mjobModel.get('et_carousels').length > 0){
                                $('.carousel-gallery').find('input[name="et_carousels"]').remove();
                            }
                            else{
                                view.$el.find('.carousel-gallery').append('<input type="hidden" name="et_carousels" value="" />');
                            }
                            if( typeof view.mjobModel.get('mjob_slider_thumbnail') !== 'undefined' && view.mjobModel.get('mjob_slider_thumbnail') != '' ){
                                $('.carousel-gallery').find('img').attr('src', view.mjobModel.get('mjob_slider_thumbnail'));
                            }

                            if (typeof tinyMCE !== 'undefined') {
                                if( null != tinymce.EditorManager.get('post_content') ) {
                                    tinymce.EditorManager.get('post_content').setContent(view.mjobModel.get('post_content'));
                                }
                                tinymce.EditorManager.execCommand('mceAddEditor', true, "post_content");
                            }

                            /**
                             * Add trigger when finish setup data for editing single mjob
                             */
                            AE.pubsub.trigger('mje:after:setup:mjob:data', view.mjobModel);
                        }
                        else {
                            view.mjobModel = new Models.Mjob();
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            reSetupSkills: function(){
                var view = this,
                    form_field = view.$('.form-group');
                form_field.find('select[name="skill"]').each(function() {
                    var $input = $(this);
                    var tax_input = view.model.get('tax_input'),
                        skills = (typeof tax_input !== 'undefined') ? tax_input['skill'] : [];
                    skill_list = [];
                    for (var i = skills.length - 1; i >= 0; i--) {
                        skill_list.push(skills[i].term_id);
                    }
                    $input.val(skill_list);
                    $input.trigger('chosen:updated');

                });
            },
            discardMjob: function(e){
                e.preventDefault();
                var view = this;
                view.$el.fadeOut(500);
                $('.mjob-single-content').fadeIn(500);
                $('html, body').animate({
                    scrollTop: $("html, body").offset().top
                }, 1000);
            },
            addExtras: function(event){
                event.preventDefault();
                var view = this;
                //Generate invalid id for temp use in template
                var model = new Models.Extra({
                    post_title:'',
                    et_budget: '',
                });
                this.extrasCollection.add(model);
                if( $('.post-service_nonce').length >0 ){
                    $('.mjob-extras-wrapper').find('input[name="_wpnonce"]').each(function(){
                        $(this).val($('.post-service_nonce').val());
                    });
                }

            },
            initExtras: function(){
                var view = this;
                // Use the code below
                if (typeof view.extrasCollection == 'undefined') {
                    //Get public  collection
                    view.extrasCollection = new Collections.Extras();
                }
                if(typeof view.extrasListView == 'undefined') {
                    view.extrasListView = new Views.ExtrasListView( {
                        collection: view.extrasCollection,
                        el: '.mjob-extras-wrapper'
                    } );
                    view.extrasListView.render();
                }
            },
            updateOpeningMessage: function(message, num_message) {
                var my_template = $('#openingMessageTemplate').html();
                if(typeof message == 'undefined')
                    return;

                if($('.opening-message .content').length <= 0 && message != '') {
                    $(my_template).insertAfter($('.mjob-single-aside .box-aside-stat'));
                    //popover-opening-message
                    $('.popover-opening-message').popover({
                        title: '<span class="pull-left icon-message"><i class="fa fa-info-circle" aria-hidden="true"></i></span><span class="pull-right text-message">'+ae_globals.title_popover_opening_message+'</span><span class="clearfix"></span>',
                        content: '<img src="'+ae_globals.assetImg+'opening-message-help.jpg" src="Opening Message" width="100%" height="auto">',
                        trigger: 'hover',
                        html: true,
                        placement: 'auto top',
                    });
                }

                if(num_message > 40) {
                    $('.opening-message .content-opening-message').addClass('hide-content gradient');
                    $('.opening-message .show-opening-message').text(ae_globals.show_bio_text);
                } else {
                    $('.opening-message .content-opening-message').removeClass('hide-content gradient');
                    $('.opening-message .show-opening-message').text('');
                }

                $('.opening-message .content-opening-message').html(message);

                if($('.opening-message .content').length > 0 && message == '') {
                    $('.opening-message').remove();
                }
            },
            submitPost: function(event) {
                event.preventDefault();
                var view 		= this,
                    $target 		= $(event.currentTarget),
                    temp 		= new Array();
                /**
                 * update model from input, textarea, select
                 */
                if (view.mjobModel.get('uploadingCarousel')) return false;
                /**
                 * update model data
                 */
                $target.find('.input-item, .wp-editor-area').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                $target.find('.tax-item').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                // trigger method before SubmitPost
                /**
                 * update input check box to model
                 */
                view.$el.find('input[type=checkbox]').each(function() {
                    var name = $(this).attr('name');
                    view.model.set(name, []);
                });
                view.$el.find('input[type=checkbox]:checked').each(function() {
                    var name = $(this).attr('name');
                    if (name == "et_claimable_check") return false;
                    if (typeof temp[name] !== 'object') {
                        temp[name] = new Array();
                    }
                    temp[name].push($(this).val());
                    view.mjobModel.set(name, temp[name]);
                });
                /**
                 * update input radio to model
                 */
                view.$el.find('input[type=radio]:checked').each(function() {
                    view.mjobModel.set($(this).attr('name'), $(this).val());
                });
                view.mjobModel.set('skill', view.skill_control.model.get('skill'));
                view.mjobModel.set('edit', 1);
                /**
                 * save model
                 */
                if( this.$("form.edit-mjob-form").validate() && view.customValidate() )  {
                    view.mjobModel.save('', '', {
                        beforeSend: function() {
                            view.blockUi.block($target.find('.btn-save'));
                            //view.loading();
                        },
                        success: function(result, res, jqXHR) {
                            view.blockUi.unblock();
                            if (res.success) {
                                if( typeof view.model.get('ID') !== 'undefined' ) {
                                    view.$el.find('input[name="post_parent"]').val(view.model.get('ID'));
                                }
                                view.extrasListView.syncChange();
                                //Edit opening message

                                view.updateOpeningMessage(res.data.opening_message, res.data.num_opening_message);

                                AE.pubsub.trigger('ae:after:edit:mjob', result, res, jqXHR);
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                            /*
							$('html, body').animate({
                                scrollTop: $("html, body").offset().top
                            }, 1000);
							*/
                        }
                    });
                }
            },
            customValidate: function(){
                var view = this;
                if(view.extrasListView.collection.models.length > 0 ) {
                    for( i =0; i< view.extrasListView.collection.models.length; i++){
                        if( parseFloat($('#et_budget_'+view.extrasListView.collection.models[i]._listenId).val()) <= 0){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.priceMinNoti,
                                notice_type: 'error'
                            });
                            return false;
                        }
                        if( $('#et_extra_'+view.extrasListView.collection.models[i]._listenId).val() == ''){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.requiredField,
                                notice_type: 'error'
                            });
                            $('#et_extra_'+view.extrasListView.collection.models[i]._listenId).focus();
                            return false;
                        }
                        if( $('#et_budget_'+view.extrasListView.collection.models[i]._listenId).val() == ''){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.requiredField,
                                notice_type: 'error'
                            });
                            $('#et_budget_'+view.extrasListView.collection.models[i]._listenId).focus();
                            return false;
                        }
                    }
                }
                return true;
            },
            initValidator: function() {
                /**
                 * post form validate
                 */
                $("form.edit-mjob-form").validate({
                    ignore: "",
                    rules: {
                        post_title: "required",
                        mjob_category: "required",
                        post_content: "required",
                        time_delivery: {
                            required: true,
                            min: 1
                        },
                        /*'et_carousels': 'required'*/
                    },
                    highlight:function(element, errorClass, validClass){
                        var $target = $(element );
                        var $parent = $(element ).parent();
                        $parent.addClass('has-error');
                        $target.addClass('has-visited');
                    },
                    unhighlight:function(element, errorClass, validClass){
                        // position error label after generated textarea
                        var $target = $(element );
                        var $parent = $(element ).parent();
                        $parent.removeClass('has-error');
                        $target.removeClass('has-visited');
                    }
                });
            },
            canNotRemove: function (model) {
                AE.pubsub.trigger('ae:notification', {
                    msg: ae_globals.min_images_notification,
                    notice_type: 'error'
                });
            }
        });
        AE.single = new Views.SingleMjob();

        /**
         * SINGLE MJOB REVIEWS
         */
        Models.ReviewItem = Backbone.Model.extend();
        Collections.ReviewList = Backbone.Collection.extend({
            action: 'mjob-fetch-review',
            initialize: function() {

            }
        });

        var reviewItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'review-item clearfix',
            template: _.template($('#review-item-loop').html()),
            onItemRendered: function() {
                this.renderRating();
            },
            // Render rating star
            renderRating: function() {
                var view = this;
                this.$el.find('.rate-it').raty({
                    readOnly: true,
                    half: true,
                    score: function() {
                        return view.model.get('et_rate');
                    },
                    hints: raty.hint
                });
            }
        });

        Views.ReviewList = Views.ListPost.extend({
            tagName: 'ul',
            itemView: reviewItem,
            itemClass: 'review-item clearfix'
        });

        var review_container = $('.review-job');
        if(review_container.length > 0) {
            if(typeof reviewCollection === "undefined") {
                if($('.review-data').length > 0) {
                    var data = JSON.parse($('.review-data').html());
                    reviewCollection = new Collections.ReviewList(data);
                } else  {
                    reviewCollection = new Collections.ReviewList();
                }
            }

            var listReview = new Views.ReviewList({
                collection: reviewCollection,
                el: review_container.find('ul')
            });

            new Views.BlockControl({
                collection: reviewCollection,
                el: review_container
            })
        }
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);

