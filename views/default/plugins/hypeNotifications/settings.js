define(function(require) {
    var $ = require('jquery');

    $(document).on('change', '.notifications-transport-selector', function() {

        if ($(this).val() === 'smtp') {
            $('.notifications-smtp-settings').removeClass('hidden');
        } else {
            $('.notifications-smtp-settings').addClass('hidden');
        }
    });
});
