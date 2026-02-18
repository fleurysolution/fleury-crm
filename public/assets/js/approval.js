(function ($) {
  'use strict';
  $(function () {
    if ($('#approvalRequestsTable').length) {
      $('#approvalRequestsTable').DataTable({
        pageLength: 10,
        order: [[0, 'desc']]
      });
    }
  });
})(jQuery);
