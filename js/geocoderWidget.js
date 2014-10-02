/**
 * @file
 * Attaches the behaviors for the Geocoder module's textfield widget.
 */

(function ($, GeocoderJS) {

"use strict";

  Drupal.behaviors.geocoder = {
    attach: function(context, settings) {
      var geocoderSettings = settings.geocoder;

      var geocoderOptions = {
        'provider': geocoderSettings.engine
      };

      if (geocoderSettings.api_key) {
        geocoderOptions.apiKey = geocoderSettings.api_key;
      }

      console.log(geocoderOptions);

      var googleGeocoder = new GeocoderJS.createGeocoder(geocoderOptions);
      for (var i in geocoderSettings.fields) {
        $('#' + geocoderSettings.fields[i].sourceField, context).autocomplete({
          source: function(request, response) {
            googleGeocoder.geocode(request.term, function(result) {
              var responseItems = [];
              for (var i in result) {
                var resultName = result[i].getCity() + ', ' + result[i].getRegion();
                responseItems.push({
                  label: resultName,
                  value: resultName,
                  lat: result[i].getLatitude(),
                  lon: result[i].getLongitude()
                });
              }
              response(responseItems);
            });
          },
          select: function(e, ui) {
            $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lat', context).val(ui.item.lat);
            $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lon', context).val(ui.item.lon);
          },
          change: function(e, ui) {
            $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lat', context).val(ui.item.lat);
            $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lon', context).val(ui.item.lon);
          }
        });
      }
    },
  };

})(jQuery, GeocoderJS);
