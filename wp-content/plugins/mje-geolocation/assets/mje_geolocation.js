
function getAddress(latLng, is_submit) {
	var geocoder = new google.maps.Geocoder();
    geocoder.geocode( {'latLng': latLng},
      function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) {
        	if(is_submit){
	          	if(results[0]) {
	            	document.getElementById("et_full_location").value = results[0].formatted_address;
	          	} else {
	            	document.getElementById("et_full_location").value = "No results";
	          	}
	        }
        }
        else {
          	document.getElementById("et_full_location").value = status;
        }
      });
    }

  (function($, Models, Collections, Views) {


	Views.mjeGeo = Backbone.View.extend({
		el: 'body',
		model: [],
        events: {
			'keyup input#et_full_location': 'gecodeMap', // in post project field.
		},
		initialize: function(options) {

			console.log('init mjegeo');
			jQuery(".slider-ranger").slider();

			 AE.pubsub.on('mje:changedRanger', this.changeRanger, this);
			// jQuery(".slider-ranger-radius").slider().on('slide', function(event){
			// 	var distance = event.value;
			// 	console.log('distance: '+ distance);
			// });

			var view = this;
			this.longitude = this.langtitude = '';
			// if( ae_globals.is_home  || ae_globals.is_search ) {
			// 	if ( navigator.geolocation ) {

	  //         		navigator.geolocation.getCurrentPosition(function(position) {

			//             var pos = {
			//               	lat: position.coords.latitude,
			//               	lng: position.coords.longitude
			//             };
			//             var is_submit = false;
			// 	        getAddress(pos, is_submit);

			// 	        document.getElementById("latitude").value = position.coords.latitude;
			// 			document.getElementById("longitude").value = position.coords.longitude;


		 //          	}, function() {
		 //          		console.log('Browser doen\'t share location');

		 //          });
		 //        }

			// } else
			if( ae_globals.is_submit_post ){
				view.map = window.map;
				this.marker = null;

				if (navigator.geolocation) {
		          	navigator.geolocation.getCurrentPosition(function(position) {

			            var pos = {
			              	lat: position.coords.latitude,
			              	lng: position.coords.longitude
			            };

			            // infoWindow.setPosition(pos);
			            // infoWindow.setContent('Location found.');
			            // infoWindow.open(map);
			            window.map.setCenter(pos);

			            window.mjeMarker = new google.maps.Marker({
				            //map: window.map,
				            draggable:true,
				            //animation: google.maps.Animation.DROP,
				            position: pos,
				            title:'auto geo',
				        });
				        console.log('possition ');
				        console.log(position);
				        window.mjeMarker.setMap(null);
				        window.mjeMarker.setMap(window.map);
				        var is_submit = true;
			            getAddress(pos, is_submit);

			            google.maps.event.addListener(window.mjeMarker, 'dragend',
							function(marker) {

							var latLng = marker.latLng;

							var currentLatitude = latLng.lat();
							var currentLongitude = latLng.lng();
							console.log(currentLatitude);
							document.getElementById("et_location_lat").value = currentLatitude;
							document.getElementById("et_location_lng").value = currentLongitude;
							// $("#la").val(currentLatitude);
							// $("#lo").val(currentLongitude);
							}
						);

		          	}, function() {
		          		console.log('Browser doen\'t share location');

		          });
		        } else {
		          	// Browser doesn't support Geolocation
		         	console.log('Browser doesn\'t support Geolocation');
		        }
		    }
		},
		changeRanger: function(obj, view ){
			view.query['latitude'] = curPos.lat;
			view.query['longitude'] = curPos.long;
			console.log(view);
			jQuery(".cur-distance").html(obj.val);
		},
		/**
         * init map gecode an address
         */
        gecodeMap: function(event) {

            var address = jQuery(event.currentTarget).val(),
                view = this;
   			var geocoder = new google.maps.Geocoder();

            //gmaps = new GMaps
            //https://developers.google.com/maps/documentation/javascript/geocoding
            if( ae_globals.is_home ) {
            	geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == 'OK') {

						var obj  = results[0];

						var latitude = obj.geometry.location.lat();
						var longitude = obj.geometry.location.lng();

						document.getElementById("latitude").value = latitude;
						document.getElementById("longitude").value = longitude;
					} else {

						console.log('eror' + status);
					}
            	});
            }

            if (window.map) {
            	geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == 'OK') {
					window.map.setCenter(results[0].geometry.location);

					var obj  = results[0];
					var address_label = obj.formatted_address;

					if(window.mjeMarker != null)
						window.mjeMarker.setMap(null);

					window.mjeMarker = new google.maps.Marker({
					   // map: window.map,
					    draggable:true,
					   // animation: google.maps.Animation.DROP,
					    position: results[0].geometry.location,
					    title: address,
					});

					var latitude = obj.geometry.location.lat();
					var longitude = obj.geometry.location.lng();

					document.getElementById("et_location_lat").value = latitude;
					document.getElementById("et_location_lng").value = longitude;

					window.mjeMarker.setMap(window.map);
					google.maps.event.addListener(window.mjeMarker, 'dragend',
						function(marker) {

						var latLng = marker.latLng;

						var currentLatitude = latLng.lat();
						var currentLongitude = latLng.lng();

						document.getElementById("et_location_lat").value = currentLatitude;
						document.getElementById("et_location_lng").value = currentLongitude;

						}
					);} else {
						console.log('geo location error:');
						console.log(status);
					// alert('Geocode was not successful for the following reason: ' + status);
					}
			    });


			}
        },

	});

	new Views.mjeGeo;
})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);