(function ($, Drupal, drupalSettings) {
  'use strict';
  Drupal.behaviors.personalization = {
    attach: function (context, settings) {
      $(".paragraph--type--smart-content-paragraph").hide();
      $('.field--name-field-default').hide();
      var url = drupalSettings.path.baseUrl + 'personalised_content/reactions/' + settings.smart_content_paragraphs.personalization.nid;
      getLocation();
      var data = {
        device_type: detect_device(),
        device_width: screen.width,
        device_height: screen.height,
        visitor_type: get_visitor_type(),
        no_of_pages_visited: parseInt(getCookie('no_of_pages_visited')),
        longest_session: get_longest_session(),
        continent: getCookie('continent'),
        country: getCookie('country'),
        country_code: getCookie('country_code'),
        region: getCookie('region'),
        latitude: getCookie('latitude'),
        longitude: getCookie('longitude'),
        cloudflare_country_code: settings.smart_content_paragraphs.personalization.cloudflare_country_code,
        pages: getPages(),
      };

      $.ajax({
        url: url,
        type: 'POST',
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        success: function (results) {
          var removables = results.data;
          $('.paragraph--type--smart').each(
            function() {
              var show_default = true;
              var main_paragraph = this;
              $.each(removables, function (key, value) {
                if ($(main_paragraph).find(".paragraph--" + value).length > 0) {
                  show_default = false;
                  $(".paragraph--" + value).show();
                }
              });
              if (show_default) {
                $(main_paragraph).find('.field--name-field-default').show();
              }
            }
          );
        }
      });
    }
  };

  /*
  * Get the list of pages viewed by the user from cookie
  */
  function getPages() {
    var pages = getCookie('pages') || [];
    var current = drupalSettings.path.currentPath;
    if (typeof pages === 'string') {
      pages = pages.split(",");
    }
    if (pages.indexOf(current) == -1) {
      pages.push(current);
    }
    setCookie('pages', pages);
    return pages;
  }

  /*
  * Detect the device whether it is Mobile/Tablet/Desktop
  */
  function detect_device() {
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(navigator.userAgent)) {
      return 'mobile';
    }
    else if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(navigator.userAgent)) {
      return 'tablet';
    }
    else {
      return 'desktop';
    }
  }

  /*
  * Get visitor type based on the cookie value
  */
  function get_visitor_type() {
    if (!getCookie('visited')) {
      setCookie("visited", '1');
      setCookie("no_of_pages_visited", '1');
      setCookie("session_start_time", Date());
      return 'first_time_visitor';
    }
    else {
      var no_of_pages_visited = parseInt(getCookie('no_of_pages_visited')) + 1;
      setCookie("no_of_pages_visited", no_of_pages_visited);
      return 'returning_visitor';
    }
  }

  /*
  * Get already existung cookie values
  */
  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  /*
  * Set cookie value along with expiration time
  */
  function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + '; path=/;';
  }

  /*
  * Get longest session of a visitor
  */
  function get_longest_session() {
    var session_time = getCookie('session_start_time');
    var current_time = Date();
    var date1, date2;

    date1 = new Date(session_time);
    date2 = new Date(current_time);

    var res = Math.abs(date1 - date2) / 1000;

    // get minutes
    var longest_session_time = Math.floor(res / 60) % 60;
    return longest_session_time;
  }

  /*
  * Get geolocation details - Browser
  */
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(setLatLong);
    }
    else {
      setCookie("latitude", '');
      setCookie("longitude", '');
    }
  }

  /*
  * Store the user''s geolocation details in cookie
  */
  function setLatLong(position) {
    setCookie("latitude", position.coords.latitude);
    setCookie("longitude", position.coords.longitude);
  }

})(jQuery, Drupal, drupalSettings);