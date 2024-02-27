(function($, Views, Models, Collections){
    $(document).ready(function() {
        Models.mJobUser = Backbone.Model.extend({
            action: 'mjob_sync_user'
        });

        // MODAL SIGN UP STEP 1: Input email
        Views.SignUpEmailModal = Views.Modal_Box.extend({
            el: '#signUpStep1',
            events: {
                'submit form' : 'nextStep'
            },
            initialize: function(options) {
                AE.Views.Modal_Box.prototype.initialize.call();
                AE.pubsub.trigger('ae:init:modal:signup', this);
                this.options = _.extend(this, options);

                if(typeof this.signUpModal === "undefined") {
                    if(typeof this.reload !== "undefined") {
                        this.signUpModal = new Views.SignUpModal({
                            reload: this.reload
                        });
                    } else {
                        this.signUpModal = new Views.SignUpModal();
                    }
                }

                this.initValidator();
            },
            nextStep: function(event) {
                event.preventDefault();
                var view = this;
                if(view.form_validator.form()) {
                    view.model = new Models.mJobUser({
                        user_email: $(event.currentTarget).find('#user_email').val(),
                        do_action: 'check_email'
                    });

                    this.model.save('', '', {
                        success: function (result, resp, jqXHR) {
                            if( resp.success ) {
                                // Close this modal
                                view.closeModal();

                                // Open next step - modal sign up
                                view.signUpModal.$el.find('form').append('<input type="hidden" id="user_email" name="user_email" value="'+ view.model.get('user_email') +'"/>');
                                // Open modal
                                view.signUpModal.openModal();
                            }
                            else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: resp.msg,
                                    notice_type: 'error'
                                });
                            }
                        }
                    });

                }
            },
            initValidator: function() {
                var view = this;
                view.form_validator = view.$el.find('form').validate({
                    rules: {
                        user_email: {
                            required: true,
                            email: true
                        }
                    },
                    errorElement: "p",
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
                })
            }
        });

        // MODAL SIGN UP
        Views.SignUpModal = Views.Modal_Box.extend({
            el: '#signUpStep2',
            initialize: function(options) {
                AE.pubsub.on('ae:form:submit:success', this.registerSuccess, this);
                this.options = _.extend(this, options);
            },
            registerSuccess: function(result, resp, jqXHR, type) {
                if(type == 'signUp') {
                    var view = this;
                    if(resp.success == true) {
                        view.closeModal();
                        if(ae_globals.user_confirm == "1") {
                            setTimeout(function() {
                                if(typeof view.reload !== "undefined" && view.reload == true) {
                                    window.location.reload();
                                } else {
                                    if(typeof resp.data.redirect_url !== 'undefined') {
                                        window.location.href = resp.data.redirect_url;
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            }, 4000);
                        } else {
                            if(typeof view.reload !== "undefined" && view.reload == true) {
                                window.location.reload();
                            } else {
                                if(typeof resp.data.redirect_url !== 'undefined') {
                                    window.location.href = resp.data.redirect_url;
                                } else {
                                    window.location.reload();
                                }
                            }
                        }
                    }
                }
            },
        });

        // MODAL SIGN IN
        Views.SignInModal = Views.Modal_Box.extend({
            el: '#signIn',
            initialize: function(options) {
                AE.pubsub.on('ae:form:submit:success', this.authSuccess, this);
                AE.pubsub.on('ae:init:modal:signup', this.close, this);
                AE.pubsub.on('ae:init:modal:forgot', this.close, this);

                this.options = _.extend(this, options);
            },
            authSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(type == 'signIn') {
                    if(resp.success == true) {
                        view.closeModal();

                        if(typeof view.reload !== 'undefined' && view.reload == true) {
                            window.location.reload();
                        } else {
                            if(typeof resp.data.redirect_url !== 'undefined') {
                                window.location.href = resp.data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }

                    }
                }
            },
            close: function() {
                var view = this;
                view.closeModal();
            }
        });

        /**
         * MODAL FORGOT PASSWORD
         */
        Views.ForgotPassword = Views.Modal_Box.extend({
            el: '#forgotPassword',
            initialize: function() {
                // do forgot password
                var forgotPasswordForm = new Views.AE_Form({
                    el: '#forgotPasswordForm',
                    model: new Models.User({
                        do: 'forgot',
                        method: 'read'
                    }),
                    rules: {
                        user_login: {
                            required: true,
                            email: true
                        }
                    },
                    type: 'forgotPassword',
                    blockTarget: '#forgotPasswordForm button'
                });

                AE.pubsub.on('ae:form:submit:success', this.sendResetPassSuccess, this);
                AE.pubsub.trigger('ae:init:modal:forgot', this);
            },
            sendResetPassSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(resp.success == true) {
                    view.closeModal();
                }
            }
        });

        /**
         * A U T H E N T I C A T I O N  V I E W
         */
        Views.Authentication = Backbone.View.extend({
            el: 'body',
            events: {
                'click a.open-signin-modal' : 'openSignInModal',
                'click a.open-signup-modal' : 'openSignUpEmailModal',
                'click a.open-forgot-modal' : 'openForgotPasswordModal',
                'click a.focus-signin-form' : 'focusSignInForm',
                'click a.focus-signup-form' : 'focusSignUpForm',
                'click .resend-email-confirm': 'resendConfirmationEmail'
            },
            initialize: function() {
                // Show form depend on hash tag
                var start = window.location.hash || '#login';
                if( start == '#login' ) {
                    this.focusSignInForm();
                } else if( start == '#register' ) {
                    this.focusSignUpForm();
                }

                // Block UI
                this.blockUi = new Views.BlockUi();

                // do sign in
                var signInForm = new Views.AE_Form({
                    el: '#signInForm', // parent of form
                    model: new Models.User({
                        do: 'login',
                        method: 'read'
                    }),
                    rules: {
                        user_login: 'required',
                        user_pass: 'required'
                    },
                    type: 'signIn',
                    blockTarget: '#signInForm button',
                    showNotice: true
                });

                // do sign up
                var signUpForm = new Views.AE_Form({
                    el: '#signUpForm',
                    model: new Models.User(),
                    rules: {
                        user_login: "required",
                        user_pass: "required",
                        repeat_pass: {
                            required: true,
                            equalTo: "#signUpForm #user_pass"
                        },
                        user_email: {
                            required: true,
                            email: true
                        },
                        term_privacy: "required",
                    },
                    type: 'signUp',
                    blockTarget: '#signUpForm button',
                    showNotice: true
                });

                // do reset password
                var resetPassForm = new Views.AE_Form({
                    el: '#resetPassForm',
                    model: new Models.User({
                        do: 'resetpass'
                    }),
                    rules: {
                        new_password: 'required',
                        repeat_pass: {
                            required: true,
                            equalTo: '#resetPassForm #new_password'
                        }
                    },
                    type: 'resetPassword',
                    blockTarget: '#resetPassForm button'
                });

                // do change password
                if($('#current_user').length > 0) {
                    var changePassModel = new Models.User(currentUser.data);
                } else {
                    var changePassModel = new Models.User();
                }
                changePassModel.set('do', 'changepass');
                var changePassForm = new Views.AE_Form({
                    el: '#changePassForm',
                    model: changePassModel,
                    rules: {
                        old_password: 'required',
                        new_password: 'required',
                        renew_password: {
                            required: true,
                            equalTo: '#changePassForm #new_password'
                        }
                    },
                    type: 'changePassword',
                    blockTarget: '#changePassForm button'
                });

                AE.pubsub.on('ae:form:submit:success', this.submitSuccess, this);
                AE.pubsub.on('mjob:open:signin:modal', this.catchOpenSignInModal, this);
            },
            catchOpenSignInModal: function(modal) {
                if(typeof modal !== 'undefined') {
                    this.signInModal = modal;
                }
            },
            openSignInModal: function(event) {
                event.preventDefault();
                if(typeof this.signInModal === 'undefined') {
                    this.signInModal = new Views.SignInModal();
                }
                this.signInModal.openModal();
                AE.App.authModal = this.signInModal;
            },
            openSignUpEmailModal: function(event) {
                event.preventDefault();

                // close signin modal
                var reload = false;
                if(typeof this.signInModal !== 'undefined') {
                    this.signInModal.closeModal();
                    if(typeof this.signInModal.reload !== 'undefined') {
                        reload = this.signInModal.reload;
                    }
                }

                if( typeof this.emailModal === 'undefined' ) {
                    this.emailModal = new Views.SignUpEmailModal({
                        reload: reload
                    });
                }

                this.emailModal.openModal();
                AE.App.authModal = this.emailModal;
            },
            openForgotPasswordModal: function(event) {
                event.preventDefault();
                if(typeof this.forgotModal === 'undefined') {
                    this.forgotModal = new Views.ForgotPassword();
                }
                // close signin modal
                if(typeof this.signInModal !== 'undefined') {
                    this.signInModal.closeModal();
                }
                this.forgotModal.openModal();
            },
            submitSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(type == 'resetPassword') {
                    if(resp.success == true && typeof resp.data.redirect_url != 'undefined') {
                        window.location.href  = resp.data.redirect_url;
                    }
                } else if((type == 'signIn'  || type == 'signUp') && (resp.success)) {
                    AE.pubsub.trigger('ae:user:auth', result, resp, jqXHR);

                    // Live update user section on main navigation
                    if ($('#mjob_my_account_header').length > 0) {
                        var template = _.template($('#mjob_my_account_header').html());
                        $('#myAccount .user-account').html(template({
                            avatar: resp.data.avatar,
                            display_name: resp.data.display_name
                        }));
                    }

                    // Prevent redirect double
                    if (typeof view.signInModal === 'undefined' && typeof view.emailModal === 'undefined') {
                        if(typeof resp.data.redirect_url != 'undefined') {
                            window.location.href = resp.data.redirect_url;
                        }
                    }
                } else if(type == 'changePassword') {
                    if(resp.success == true) {
                        if(typeof resp.data.redirect_url != 'undefined') {
                            window.location.href = resp.data.redirect_url;
                        }
                    }
                }
            },
            focusSignInForm: function() {
                var signInForm = $('.form-auth-page').find('#signInForm');
                var signUpForm = $('.form-auth-page').find('#signUpForm');

                signUpForm.hide();
                signInForm.fadeIn();
                signInForm.find('#user_login').focus();

                return false;
            },
            focusSignUpForm: function(event) {
                var signInForm = $('.form-auth-page').find('#signInForm');
                var signUpForm = $('.form-auth-page').find('#signUpForm');

                signInForm.hide();
                signUpForm.fadeIn();
                signUpForm.find('#user_email').focus();

                return false;
            },
            resendConfirmationEmail: function(event) {
                event.preventDefault();
                var target = $(event.currentTarget);
                var view = this;
                if(typeof currentUser.data !== 'undefined') {
                    var userModel = new Models.User(currentUser.data);
                    userModel.save({do: 'confirm_mail'}, {
                        beforeSend: function() {
                            view.blockUi.block(target);
                        },
                        success: function(status, res, xhr) {
                            if(res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'success',
                                    msg: res.msg
                                });

                                $('.active-account').remove();
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'error',
                                    msg: res.msg
                                });
                            }
                            view.blockUi.unblock();
                        }
                    })
                } else {
                    AE.pubsub.trigger('ae:notification', {
                        notice_type: 'error',
                        msg: ''
                    })
                }
            }
        });
        new Views.Authentication();
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections);