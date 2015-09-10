;(function($) {

    "use strict";

    var envatoAPI = {
        init: function() {
            this.purchaseForm();
        },

        purchaseForm: function() {

            var form = $('#ss-envato-verify-form');

            form.submit(function(e) {
                e.preventDefault();

                form.find('.msgs-list').remove();

                form.addClass('loading-ajax');

                if($(this).find('#ss-envato-license').val() == '') return;

                var data = $(this).serialize();

                data += '&action=ss_handle_purchase_code';

                console.log(data);

                $.ajax({
                    url: ss_envato.ajax_url,
                    method: 'post',
                    data: data,
                    success: function(r) {
                        form.prepend(r);
                    },
                    error: function() {
                        console.log('error');
                    },
                    complete: function(r) {
                        form.removeClass('loading-ajax');
                        console.log('complete');
                    },
                });



            });
        },

    };

    
    $(document).ready(function(){
        envatoAPI.init();
    });

})(jQuery);