(function(Views, Models, $, Backbone, Collections) {
    Views.SearchAdvance = Backbone.View.extend({
    	el: 'body',
        initialize: function() {
            AE.pubsub.on('ae:on:after:fetch', this.scrollTop, this);
            AE.pubsub.on('mje:getParamSearch', this.getParamSearch, this); // change url if search page.

        },
        getParamSearch : function(params){
        	//console.log('getParamSearch Processing:');
        	var view = this;
        	if( ae_globals.is_search ){
        		console.log('is_search ');
	        	/*var key = params.val == 'date'? params.val : params.name;
	        	var value = params.val == 'date'? params.sort : params.val;*/
	        	var new_url = view.paramUpdate({
				    key: params.name,
				    value: params.val,
				    sort: params.sort
				});
	        	console.log('new_url');
				console.log(new_url);

	            window.history.pushState('', '',new_url );
            } else {
            	// is_taxonomy page và chỉ call khi thay đổi các custom filter.
            	// khong call nếu thay đổi link mjob_cat.
            	//console.log('change filter in mjob_cat page . Param: ');
            	//console.log(params);

            	var term_url = ae_globals.term_link;
            	if( params){

            		if ( params.name == 'skill' ){
            			var temp = "skill_ids";
            			var ids = params.val.join();
            			var new_url = term_url+'?'+temp+'=' + ids;


            		} else{
            			//console.log(params.name);
            		}
            	}

            	//window.history.pushState('', '',new_url );
            }
        },
        scrollTop : function(params){

        	var view = this;
        	if(ae_globals.is_search || ae_globals.is_tax_mjob_category) {
        		$('html, body').animate({
                	scrollTop: view.$el.offset().top - 180
            	}, 800);
        	}
        	if(ae_globals.is_tax_mjob_category){
        		//console.log('change url after fetch.');
        		//console.log(params);
        	}
        },
        paramUpdate: function(param, default_url = '') {

          	var view = this;
	        var url = window.location.href;
	        if( ae_globals.term_link)
	        	url =  ae_globals.term_link;

	        if(param.key == "language")
      		{
      			param.key = 'language_ids';
      		}
      		if(param.key == "skill")
      		{
      			param.key = 'skill_ids';
      		}
      		if(param.key == "mjob_category") {
      			console.log('paramUpdate for key = mjob_category.');
      			var regExp = new RegExp('s' + '=([a-z0-9\-\_]+)(?:&)?'),
      			existsMatch = url.match(regExp);
      			if(existsMatch != null){
      				var paramToUpdate = existsMatch[0],
	 		      	valueToReplace = existsMatch[1],
	 	      		updatedParam = paramToUpdate.replace(valueToReplace,'');
		     		url = url.replace(paramToUpdate, updatedParam);
      			}
      			$('#input-search').val('');
      		}
	        url = view.removeParam(param.key,url);
	        if( param.value != null && param.value != '') {
	          	if(param.key == 'et_budget') {
	          		url = view.removeParam('price_min',url);
	          		url = view.removeParam('price_max',url);
	          		var budget = (param.value).split(","),
	          		 min = budget[0],
       				 max = budget[1],
	          		 str = '';
	          		if(min != '')
	          		{
	          			str += '&price_min=' + min;
	          		}
	          		if(max != '')
	          		{
	          			str += '&price_max=' + max;
	          		}
	          		url = url + str;
	          	} else {
	          		url =  url + '&' + param.key + '=' + param.value;
	          	}
	        } else {
	        	if(view.checkParam('price_min', url ))
	        		url = view.removeParam('price_min',url);
	        	if(view.checkParam('price_max', url ))
	          		url = view.removeParam('price_max',url);
	        }
	  	  	if(param.sort) {
			  	if(!view.checkParam('sort', url ))
			  	    url = url + '&sort=' + param.sort;
			  	 else
			  	 {
			  	 	url = this.removeParam('sort',url);
			  	 	url = url + '&sort=' + param.sort;
			  	 }
			} else {
		  	  	url = this.removeParam('sort',url);
		  	}
			return url;
		},
	 	removeParam: function(key, sourceURL) {
		    var rtn = sourceURL.split("?")[0],
		        param,
		        params_arr = [],
		        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
		    if (queryString !== "") {
		        params_arr = queryString.split("&");
		        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
		            param = params_arr[i].split("=")[0];
		            if (param === key) {
		                params_arr.splice(i, 1);
		            }
		        }
		        rtn = rtn + "?" + params_arr.join("&");
		    }
		    return rtn;
		},
		checkParam: function(field , url){
			if(url.indexOf('?' + field + '=') != -1)
			    return true;
			else if(url.indexOf('&' + field + '=') != -1)
			    return true;
			return false;
		}
    });
    $(document).ready(function () {
       new Views.SearchAdvance();
        $('.filter-budget-min').on('keypress input', function(event){
          var key = window.event ? event.keyCode : event.which;
			    if (event.keyCode === 8 || event.keyCode === 46) {
			        return true;
			    } else if ( key < 48 || key > 57 ) {
			        return false;
			    } else {
			    	return true;
			    }
      	});
        $('.filter-budget-max').on('keypress input', function(event){
        	var key = window.event ? event.keyCode : event.which;
         		if (event.keyCode === 8 || event.keyCode === 46) {
			        return true;
			    } else if ( key < 48 || key > 57 ) {
			        return false;
			    } else {
			    	return true;
			    }
      	});
        $('.filter-open-btn, .filter-close-btn').on('click', function(ev) {
        	ev.preventDefault();
        	$('body').toggleClass('filter-open');
        	var _hasfilter = $('body').hasClass('filter-open');
        	if(_hasfilter) {
        		$('body').append('<div class="filter-backdrop fade in"></div>');
        	} else {
        		$('body').find('.filter-backdrop').remove();
        	}
        	$('.mje-col-left-wrap').toggleClass('active');
        });
    });


})(AE.Views, AE.Models, jQuery, Backbone, AE.Collections);