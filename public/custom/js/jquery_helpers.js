let $ = window.jQuery;

/**
 * converts a form into a json object.
 * usage: $('#some-form').serializeObject();
 * If name of a field repeats, this function converts it into an array
 */
$.fn.serializeObject = function () {
  let o = {};
  let a = this.serializeArray();
  $.each(a, function () {
    if (o[this.name]) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || '');
    } else {
      o[this.name] = this.value || '';
    }
  });
  return o;
};

/**
 * Change the status of a button into loading.
 * Disables the button and change the btn text into `data-loading-text`
 * attribute of the element (else default string will be taken).
 * usage : $('#submit-btn').loading();
 */
$.fn.loading = function () {
  this.data('original', this.html());
  this.addClass('disabled');
  let loadingText = this.data('loading-text');
  if (!loadingText)
    loadingText = 'Processing';
  let loadingHTML = '<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;&nbsp;' + loadingText;
  this.html(loadingHTML);
};

/**
 * Reverts the state of a button in loading state in to normal state.
 * $('#submit-btn').resetLoading() is opposite of  $('#submit-btn').loading();
 */
$.fn.resetLoading = function () {
  this.removeClass('disabled');
  this.html(this.data('original'));
};

//Functions to check the screen sizes in javascript
// Alternative to $.Pages.isVisibleXs(), $.Pages.isVisibleSm() etc.
$.extend({
  isScreenSizeXs: function () {
    return (screen.width < 768);
  },
  isScreenSizeSm: function () {
    return (screen.width >= 768 && screen.width < 992);
  },
  isScreenSizeMd: function () {
    return (screen.width >= 992 && screen.width < 1200);
  },
  isScreenSizeLg: function () {
    return (screen.width >= 1200);
  },
  getNotificationPosition: function () {
    if ($.isScreenSizeXs() || $.isScreenSizeSm()) {
      return 'top';
    } else {
      return 'top-right';
    }
  },
  getNotificationStyle: function () {
    if ($.isScreenSizeXs() || $.isScreenSizeSm()) {
      return 'bar';
    } else {
      return 'flip';
    }
  },
  showNotification(message, type, timeout) {
    $('body').pgNotification({
      style: $.getNotificationStyle(),
      position: $.getNotificationPosition(),
      message: message,
      timeout: timeout,
      type: type || 'success',
    }).show();
  },

  /**
   * success - shows success notification
   *
   * @param  {String} message message to be displayed
   */
  success(message) {
    $.showNotification(message, 'success', 4000);
  },
  /**
   * error - shows error notification
   *
   * @param  {String} message message to be displayed
   */
  error(message) {
    $.showNotification(message, 'danger', 4000);
  },
  /**
   * info - shows info notification
   *
   * @param  {String} message message to be displayed
   */
  info(message) {
    $.showNotification(message, 'info', 4000);
  },
  /**
   * warning - shows warning notification
   *
   * @param  {String} message message to be displayed
   */
  warning(message) {
    $.showNotification(message, 'warning', 4000);
  },

  /**
   * initSelectFx - to modify the style of selection box
   *
   * @return {type}  description
   */
  initSelectFx() {
    $('select.cs-select').each(function () {
      /* global SelectFx */
      if (!$(this).data('init-plugin')) {
        new SelectFx(this, {
          onChange: function (element) {
            setTimeout(function () {
              $(element).trigger('change');
            }, 0);
          }
        });
        $(this).data('init-plugin', 'select-fx');
      }
    });
  },
  initCheckBox() {
    $('body').off('click', '.checkbox label').on('click', '.checkbox label', function (event) {
      if (!$(this).siblings('input[type=checkbox]').prop('disabled'))
        $('#' + $(this).prop('for')).prop('checked', !$('#' + $(this).prop('for')).prop('checked'));
      // $('#' + $(this).prop('for')).prop('checked', !$(this).siblings('input[type=checkbox]').prop('checked'));
    });
  },
  initSummernoteCheckBox() {
    $('body').off('click', '.checkbox label').on('click', '.checkbox label', function (event) {
      event.preventDefault();
      if (!$(this).siblings('input[type=checkbox]').prop('disabled'))
        $('#' + $(this).prop('for')).prop('checked', !$('#' + $(this).prop('for')).prop('checked'));
      // $('#' + $(this).prop('for')).prop('checked', !$(this).siblings('input[type=checkbox]').prop('checked'));
    });
  }
});

$.fn.extend({
  /**
   * animateCss - to animate a dom element using animate.css
   *    https://github.com/daneden/animate.css
   * @param  {String} animationName   name of the animation
   * @param  {Function} callback
   */
  animateCss: function (animationName, callback) {
    let animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
    $(this).addClass('animated ' + animationName).one(animationEnd, function () {
      $(this).removeClass('animated ' + animationName);
      if (typeof callback === 'function') {
        callback();
      }
    });
  },
  /**
   * waitTillVisible - Wait till the element is visible and executes the callback
   *
   * @param  {function} callback callback function
   */
  waitTillVisible: function (callback) {
    let checkExist = setInterval(function () {
      if ($(this).length) {
        callback();
        clearInterval(checkExist);
      }
    }, 100);
  }
});
