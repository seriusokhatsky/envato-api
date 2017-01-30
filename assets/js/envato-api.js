;(function($) {

    "use strict";

    var envatoAPI = {
        init: function() {
            this.purchaseForm();
            this.updatePurchaseAction();
            this.deletePurchaseAction();
        },

        updatePurchaseAction: function() {
            $('body').on('click', '.ss-update-code-btn', function(e) {
                e.preventDefault();

                var $btn = $(this),
                    code = $btn.data('code');

                if( $btn.hasClass('ss-loading') ) return;

                $btn.addClass('ss-loading');

                $.ajax({
                    url: ss_envato.ajax_url,
                    method: 'get',
                    data: {
                        action: 'ss_update_purchas_code',
                        code: code
                    },
                    success: function(r) {
                        $('.ss-update-codes').prepend(r);
                    },
                    error: function() {
                        console.log('error');
                    },
                    complete: function(r) {
                        $btn.removeClass('ss-loading');
                        console.log('complete');
                    },
                });


            });
        },

        deletePurchaseAction: function() {
            $('body').on('click', '.remove-purchase-code', function(e) {
                e.preventDefault();

                var $btn = $(this),
                    id = $btn.data('id');

                if( $btn.hasClass('ss-loading') ) return;

                $btn.addClass('ss-loading');

                $.ajax({
                    url: ss_envato.ajax_url,
                    method: 'get',
                    data: {
                        action: 'ss_delete_purchase_code',
                        id: id
                    },
                    success: function(r) {
                        $btn.parent().before(r);
                        $btn.parent().hide();
                    },
                    error: function() {
                        console.log('error');
                    },
                    complete: function(r) {
                        $btn.removeClass('ss-loading');
                        console.log('complete');
                    },
                });


            });
        },

        purchaseForm: function() {

            var form = $('#ss-envato-verify-form'),
                loading = false;

            form.submit(function(e) {
                e.preventDefault();

                if( loading || $(this).find('#ss-envato-license').val() == '' ) return;

                loading = true;

                form.find('.msgs-list').remove();

                form.addClass('loading-ajax');

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
                        loading = false;
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