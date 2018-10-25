(function($) {
  'use strict';

  // Rating
  hivepress.getObject('rating').each(function() {
    var field = $(this);

    field.raty({
      starType: 'i',
      starOff: 'fas fa-star',
      starHalf: 'fas fa-star active',
      starOn: 'fas fa-star active',
      hints: ['', '', '', '', ''],
      noRatedMsg: '',
      readOnly: typeof field.data('id') === 'undefined',
      scoreName: field.data('id'),
      score: field.data('value'),
    });
  });
})(jQuery);