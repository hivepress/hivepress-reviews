(function($) {
  'use strict';

  // Rating
  hivepress.getObject('rating').each(function() {
    var field = $(this);

    field.raty({
      starType: 'i',
      starHalf: 'fas fa-star-half active',
      starOff: 'fas fa-star',
      starOn: 'fas fa-star active',
      hints: ['', '', '', '', ''],
      noRatedMsg: '',
      readOnly: typeof field.data('id') === 'undefined',
      scoreName: field.data('id'),
      score: field.data('value'),
    });
  });
})(jQuery);