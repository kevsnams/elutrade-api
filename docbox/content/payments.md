## Paypal

There's a `{BASE_URL}/test-paypal` where you can test Paypal payment.

To get started, first we need to inject this script:  
`<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=PHP></script>`  

Note that we need to add `&currency=PHP`

Read more:
- https://developer.paypal.com/docs/checkout/integrate
- https://developer.paypal.com/docs/checkout/reference/customize-sdk

### Create Order
`auth: required`
```endpoint
POST /api/v1/transaction/payment/paypal/create
```

This creates a paypal order.  
Make sure you return a promise on `createOrder()` callback and return paypal_order_id on `Promise.then()`.  

You can use either native `fetch()` or `axios`.

```javascript
paypal.Buttons({
    createOrder: function() {
        return axios.post('{BASE_URL}/api/v1/transaction/payment/paypal/create', {
            transaction_id: 1234
        }).then(function (response) {
            // This is very important!
            // because paypal will pass this to onApprove() callback
            return response.data.paypal_order_id
        });
    }
}).render('#paypal-button-container');

```

**Request**

Property | Description
---|---
`transaction_id` | (Required) The transaction id

**Response**

```
{
    "success": true,
    "paypal_order_id": "ABC123DEF456"
}
```

### Capture Payment
`auth: required`
```endpoint
POST /api/v1/transaction/payment/paypal/capture
```

This captures the paypal payment.  
You need to make sure to return a `Promise`

```javascript
paypal.Buttons({
    createOrder: function() {
        // Create order
    },

    /**
     * If the user paid the order, capture it
     */
    onApprove: function (data) {
        // data will contain some information from paypal

        return axios.post('{BASE_URL}api/v1/transaction/payment/paypal/capture', {
            transaction_id: 1234,
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
             */
        });
    }
}).render('#paypal-button-container');
```

**Requests**

Property | Description
---|---
`transaction_id` | (Required) The transaction id
`order_id` | (Required) The Paypal order id. You can get this on the first parameter of `onApprove()` callback


**Response**

```
{
    "success": true,
    "transaction": {
        // Returns the updated transaction information
        // 'payment' will also be added in this property,
        // it will contain all the Paypal payment information
    }
}
```

### Cancel Payment
`auth: required`
```endpoint
POST /api/v1/transaction/payment/paypal/cancel
```

We need to make a HTTP request if the user cancels so we can log it on our server.

```javascript
paypal.Buttons({
    createOrder: function() {
        // Create order
    },

    onApprove: function (data) {
        // Capture payment
    },

    /**
     * If the user cancels the payment, this callback will be fired
     */
    onCancel: async function (data) {
        // Paypal doesn't actually require us to send a cancel request
        // This is just to add a log on our end
        await axios.post('<?= url('api/v1/transaction/payment/paypal/cancel') ?>', {
            transaction_id: 1
        });

        // Do whatever you want here if the user cancels payment
    }
}).render('#paypal-button-container');
```

**Request**

Property | Description
---|---
`transaction_id` | (Required) The transaction id

**Response**
```
{
    "success": true
}
```
