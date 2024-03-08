/**
 * backend user, control user list in user manage list
 * search user by name
 * filter user by role
 * filter user by another data
 * use Collections.Users , View.UserItem, Models.User
 * use Views.BlockUi add view block loading
 */
(function(Models, Views, Collections, $, Backbone) {
	Views.UserList = Backbone.View.extend({
		events: {
			'click a.page-numbers' : 'pagination',
			'keyup input.user-search': 'search',
			'change select.et-input': 'search',
			'submit .et-member-search form': 'submit',
			'click .sort-link' : 'sort_link',
			'click #filter-status' : 'search'
		},

		initialize: function(options) {
			_.bindAll(this, 'addAll', 'addOne');

			var view = this;
			/**
			 * init collection data
			 */
			if ($('#ae_users_list').length > 0) {
				var users = JSON.parse($('#ae_users_list').html());
				this.Users = new Collections.Users(users.users);
				this.pages = users.pages;
				this.query = users.query;
			} else {
				this.Users = new Collections.Users();
				this.query = {};
			}
			this.paged = 1;

			this.user_view = [];
			/**
			 * init UserItem view
			 */
			this.Users.each(function(user, index, col) {
				var el = $('li.et-member').eq(index);
				view.user_view.push(new Views.UserItem({
					el: el,
					model: user
				}));
			});

			// bind event to collection users
			this.listenTo(this.Users, 'add', this.addOne);
			this.listenTo(this.Users, 'reset', this.addAll);
			this.listenTo(this.Users, 'all', this.render);

			this.blockUi = new Views.BlockUi();

			if(typeof options.item_wrapper !== 'undefined') {
				this.item_wrapper = options.item_wrapper;
			} else {
				this.item_wrapper = 'ul';
			}
		},
		/**
		 * add one
		 */
		addOne: function(user) {
			var userItem = new Views.UserItem({
				model: user
			});
			this.user_view.push(userItem);

			this.$(this.item_wrapper).append(userItem.render().el);
		},

		/**
		 * add all
		 */
		addAll: function() {
			for (var i = 0; i < this.user_view.length - 1; i++) {
				// this.user_view[i].$el.remove();
				this.user_view[i].remove();
			}

			this.$(this.item_wrapper).html('');
			this.user_view = [];
			this.Users.each(this.addOne, this);
		},
		/**
		 * build ajax params for ajax
		 */
		buildParams: function(reset) {
			unConfirm = null;
			if(this.$('#filter-status:checked').val() == 1) {
				var unConfirm = 1;
			}
			var view = this,
				keywork = this.$('input.user-search').val(),
				role = this.$('select.user-role').val(),
				sortTime = view.sortTime,
				sortDelivery = view.sortDelivery,
				// get ajax params from AE globals
				ajaxParams = AE.ajaxParams;


			if (!reset) {
				$target = this.$('.page-numbers');
			} else {
				$target = this.$(this.item_wrapper);
			}

			ajaxParams.success = function(result, status, jqXHR) {
				var data = result.data;
				view.blockUi.unblock();

				// if (reset)
				view.Users.reset();
				view.Users.set(data);

				view.$('.paginations-wrapper').html(result.paginate);

				if (data.length == 0) view.$(view.item_wrapper).html('<p class="no-items">' + result.msg + '</p>');
			};

			ajaxParams.beforeSend = function() {
				view.blockUi.block($target);
			};
			/**
			 * filter param
			 */
			ajaxParams.data = {
				search: keywork,
				paged: view.paged,
				sortTime : sortTime,
				sortDelivery: sortDelivery,
				unConfirm : unConfirm,
			};

			_.extend(ajaxParams.data, view.query);
			if (role != '') ajaxParams.data.role = role;

			ajaxParams.data.action = 'ae-fetch-users';

			return ajaxParams
		},

		/**
		 * load more user event
		 */
		pagination: function(e) {
			e.preventDefault();
			var view = this,
				$target = $(e.currentTarget);
			
			if($target.hasClass('next'))
				this.paged = parseInt(this.paged) + 1;
			else if($target.hasClass('prev'))
				this.paged = parseInt(this.paged) - 1;
			else
				this.paged = $target.html();

			var ajaxParams = this.buildParams(false);
			$.ajax(ajaxParams);

			$('html, body').animate({
				scrollTop: view.$el.offset().top - 180
			}, 800);
		},
		/**
		 * search user
		 */
		search: function(e) {
			this.paged = 1;
			var ajaxParams = this.buildParams(true);
			$.ajax(ajaxParams);
		},
		/* Prevent enter key */
		submit: function(event) {
			event.preventDefault();
		},
		sort_link: function(event) {
			var view = this;
			event.preventDefault();
			var $target = $(event.currentTarget),
				orderBy = $target.attr('data-sort'),
				order = $target.attr('data-order');

			if($target.find('i.fa').length == 0)
				$target.append('<i class="fa"></i>');
			view.paged = 1;
			if(orderBy == 'sort_time') {
				view.sortTime = order;
				view.sortDelivery = null;
			} else if(orderBy == 'sort_delivery') {
				view.sortDelivery = order;
				view.sortTime = null;
			}

			$('a.sort-link i.fa').removeClass('fa-sort-desc').removeClass('fa-sort-asc');
			if(order == 'desc') {
				$target.attr('data-order','asc');
				$target.find('i.fa').removeClass('fa-sort-desc').addClass('fa-sort-asc');
			} else {
				$target.attr('data-order','desc');
				$target.find('i.fa').removeClass('fa-sort-asc').addClass('fa-sort-desc');
			}

			var ajaxParams = this.buildParams(true);
			$.ajax(ajaxParams);

		}

	});

})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);