## User - Transactions
`auth: required`

Getting the user's transactions

```endpoint
GET /api/v1/user/{hash_id}/transactions
```
```javascript
import axios from 'axios';

const qs = required('query-string');
const props = qs.stringify({
    page: {
        size: 15, // 15 transactions per page
        number: 2 // Show page 2
    },

    include: 'buyer,payment', // Get associated buyer and seller

    sort: 'amount', // amount ASC

    as: 'seller' // Get transactions of user as a seller
});

axios.get(`{BASE_URL}/api/v1/user/{hash_id}/transactions/${props}`);
```
**Request**

Property | Description
---|---
`page[size]` | Number of items to display per page
`page[number]` | The page number you want to jump to. Defaults to page 1
`include` | A comma separated value of related data. Values allowed are: `buyer`, `seller` and `payment`. Returns `null` if nothing is associated
`sort` | The attribute of which to sort by. Values allowed are: `amount`, `created_at` and `updated_at`. By default it is `ASC`, to use `DESC` just prefix the value with `-` dash symbol. Example: `-created_at` will apply `created_at DESC`. Default: `-updated_at`
`as` | This property determines if you want to get the user's transaction as a `buyer` or `seller`. Default: `seller`

**Response**

Property | Details
---|---
`data` | The transactions data. Returns an empty array if no data can be found
`links` | The pagination links
`meta` | The metadata of the result set. Current page, last page, total, etc..

## Transaction - Logs
`auth: required`
```endpoint
GET api/v1/transaction/{hash_id}/logs
```
```javascript
import axios from 'axios';

const qs = required('query-string');
const props = qs.stringify({
    page: {
        size: 15, // 15 transactions per page
        number: 2 // Show page 2
    },

    include: 'transaction', // Get associated transaction

    sort: '-created_at', // created_at DESC
});

axios.get(`{BASE_URL}/api/v1/user/{hash_id}/transactions/${props}`);
```
Gets the logs in a transaction

**Request**

Property | Description
---|---
`page[size]` | Number of items to display per page
`page[number]` | The page number you want to jump to. Defaults to page 1
`include` | A comma separated value of related data. The only allowed value is `transaction`
`sort` | The attribute of which to sort by. Values allowed are: `created_at` and `updated_at`. By default it is `ASC`, to use `DESC` just prefix the value with `-` dash symbol. Example: `-created_at` will apply `created_at DESC`. Default: `-updated_at`

**Response**

Property | Details
---|---
`data` | The transactions data. Returns an empty array if no data can be found
`links` | The pagination links
`meta` | The metadata of the result set. Current page, last page, total, etc..
