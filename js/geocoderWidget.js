/**
 * @file
 * Attaches the behaviors for the Color module.
 */

(function ($, GeocoderJS) {

"use strict";

  Drupal.behaviors.geocoder = {
    attach: function(context, settings) {
      var geocoderSettings = settings.geocoder;

      var googleGeocoder = new GeocoderJS.createGeocoder(geocoderSettings.engine);
      for (var i in geocoderSettings.fields) {
        $('#' + geocoderSettings.fields[i].sourceField, context).autocomplete({
          source: function(request, response) {
            console.log(request);
            googleGeocoder.geocode(request.term, function(result) {
              console.log(result);
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
            /*googleGeocoder.geocode($(this).val(), function(result) {
              $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lat', context).val(result[0].latitude);
              $('#' + geocoderSettings.fields[i].destinationField + ' .geofield-lon', context).val(result[0].longitude);
            });*/
          }
        });
      }
    },
  };

})(jQuery, GeocoderJS);