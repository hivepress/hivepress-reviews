(function($) {
  'use strict';

  // Rating
  hivepress.getObject('rating').each(function() {
    $('<div />').insertAfter($(this)).raty({
      starType: 'i',
      starHalf: 'fas fa-star-half',
      starOff: 'fas fa-star-half',
      starOn: 'fas fa-star',
      hints: ['', '', '', '', ''],
      noRatedMsg: '',
      scoreName: function() {
        // todo
        return 'rating';
      },
    });
  });
})(jQuery);