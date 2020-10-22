<!DOCTYPE html>
<html>
<head>
    <title>Paypal TEST</title>
    <style>
        .log {
            width: 100%;
            height: 300px;
            resize: none;
        }
    </style>
</head>
<body>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=PHP"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <div id="paypal-button-container"></div>
    <br>
    <h5>Request/Response log</h5>
    <textarea id="log" class="log" readonly></textarea>

    <script>
        function printLog(line) {
            document.getElementById('log').value += line +"\n";
        }

        let tmpAuthToken = '1|ty7qaHMHANu8achYKbTyfjkoBHb5sn1i9qYKCiZ1';

        // i-make sure na ang buyer kay ang buyer sa transaction
        let tmpTransactionId = 1;

        /**
         * Pag kuha sa ug access_token sa buyer
         *
        axios.post(url('api/v1/auth'), {
            email: '', // email sa buyer
            password: 'password',
            device_name: 'IPhone XD 420'
        }).then(function (response) {
            // Isulod ang value ani sa tmpAuthToken
            console.log(response.data.access_token);
        });

         // Once na copy na ang auth token,
         // tanggala na ni
        */

        axios.defaults.headers.common['Authorization'] = `Bearer ${tmpAuthToken}`;

        paypal.Buttons({
            onInit: function () {
                printLog('[1] Initialized Paypal buttons');
            },

            onClick: function() {
                printLog('[2] Clicked "Pay" button');
            },

            createOrder: function () {
                printLog('[3] Created order, opening Paypal popup...');

                return axios.post('<?= url('api/v1/transaction/payment/paypal/create') ?>', {
                    transaction: tmpTransactionId,
                    mode: 'paypal'
                }).then(function (response) {
                    printLog('[3.1] Successfully created order! Check dev console for response data');

                    /**
                     * Importante nga i-return ang paypal_order_id
                     */
                    return response.data.paypal_order_id;
                });
            },

            onApprove: function (data) {
                printLog('[4] User paid!');

                return axios.post('<?= url('api/v1/transaction/payment/paypal/capture') ?>', {
                    transaction: tmpTransactionId,
                    order_id: data.orderID
                }).then(function (response) {
                    printLog('[4.1] Successful payment');
                    /**
                     * Do whatever you want sa response data
                     * expected response
                     * {
                     *     success: true,
                     *     transaction: {...}
                     * }
                     *
                     * so ang style ani, if null ang transaction.payment,
                     * means wala pa ka bayad. If naay sulod ang payment,
                     * means naka bayad na
                     */
                });
            },

            onCancel: async function (data) {
                printLog('[5] User cancelled! Sending cancel XHR.');
                await axios.post('<?= url('api/v1/transaction/payment/paypal/cancel') ?>', {
                    transaction: 1
                });
                printLog('[6] DONE!');

                /**
                 * Do whataver you want,
                 * pero make sure nga ni XHR sa ka sa paypal/cancel endpoint ^
                 * no need na nga kuhaon ang response ana, for logging purposes ra na
                 */
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
