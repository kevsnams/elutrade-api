## Auth
This section will help you through the signup, login and logout process.

### Login
```endpoint
POST api/v1/auth
```  

**Request**

Property | Description
---|---
`email` | (Required) The email used for login
`password` | (Required) The password used for login
`device_name` | (Required) The device the user is using. *DO NOT* hard code this, use device generated string.

**Response**

Property | Description
---|---
`success` | true/false
`access_token` | The token needed for Authorization. Example: `"Authorization": "Bearer {access_token}`

If this fails on validation, the message will only appear on `email` parameter.

```
{
    "message": "The given data is invalid",
    "errors": {
        "email": [
            "The username or password is incorrect"
        ]
    }
}
```

### Logout
`auth: required`  
```endpoint
POST api/v1/auth/logout
```
```javascript
import axios from 'axios';

axios.post('{BASE_URL}/api/v1/auth/logout');
```
**Request**

NONE

**Response**
```
{
    "success": true
}
```

### Signup
```endpoint
POST api/v1/signup/email
```
```javascript
import axios from 'axios';

axios.post('{BASE_URL}/api/v1/signup/email', {
    email: 'kevin@example.com',
    first_name: 'James',
    last_name: 'Bond',
    password: 'password007',
    password_confirm: 'password007',
});
```
**Request**

Property | Description
---|---
`email` | (Required) New user's email address. Max length 255 chars
`first_name` | (Required) New user's first name. Max length 255 chars
`last_name` | (Required) New user's last name. Max length 255 chars
`password` | (Required) New user's password
`password_confirm` | (Required) Password confirmation, a repeat of `password` both must be the same.

**Response**

Successful signup response
```
{
    "success": true
}
```

For error validation response, see [Validation Errors](#validation-errors)

## AuthUser
`auth: required`
```endpoint
GET api/v1/auth/user
```
Fetches the current authenticated user.  
```javascript
import axios from 'axios';

axios.get('{BASE_URL}/api/v1/auth/user)
```

**Request**

NONE

**Response**
```
{
    "success": true,
    "user": {
        // user info here
    }
}
```

## Transactions

### Create
`auth: required`
```endpoint
POST api/v1/transactions
```
Creates a transaction.

```javascript
import axios from 'axios';

axios.post('{BASE_URL}/api/v1/transactions', {
    buyer: 1234, // or NULL
    amount: '123.45' // or 123.45
});
```

**Request**

Property | Description
---|---
`buyer` | (Required) Either Integer or NULL. Should be present **but** can be null (no buyer). If `buyer` is an Integer, then it will look for the `User.id`
`amount` | (Required) The transaction amount

**Response**

Property | Description
---|---
`success` | true
`data` | The transactions data. Returns an empty array if no data can be found

For error validation response, see [Validation Errors](#validation-errors)

### Fetch Many
`auth: required`
```endpoint
GET api/v1/transactions
```
Fetches all transactions of the seller. The response JSON is paginated.

**Request**

Property | Description
---|---
`page[size]` | Number of items to display per page
`page[number]` | The page number you want to jump to. Defaults to page 1
`include` | A comma separated value of related data. Values allowed are: `buyer`, `seller` and `payment`. Returns `null` if nothing is associated
`sort` | The attribute of which to sort by. Values allowed are: `created_at` and `updated_at`. By default it is `ASC`, to use `DESC` just prefix the value with `-` dash symbol. Example: `-created_at` will apply `created_at DESC`. Default: `-updated_at`
`filter[of_buyer]` | If you want to only show transactions of a specific buyer. Example: `filter[of_buyer]=BUYER_HASH_ID_HERE`


```javascript
import axios from 'axios';

const qs = required('query-string');
const props = qs.stringify({
    page: {
        size: 15, // 15 transactions per page
        number: 2 // Show page 2
    },

    include: 'buyer,payment', // Get associated buyer and seller

    sort: '-created_at', // created_at DESC

    filter: {
        of_buyer: 'BUYER_HASH_ID' // Will only show transactions for a specific buyer
    }
});

axios.get(`{BASE_URL}/api/v1/transactions${props}`);
```

**Response**

Property | Details
---|---
`data` | The transactions data. Returns an empty array if no data can be found
`links` | The pagination links
`meta` | The metadata of the result set. Current page, last page, total, etc..


### Fetch One
```endpoint
GET apit/v1/transactions/{hash_id}
```
Fetches a single transaction by its {hash_id}  

```javascript
import axios from 'axios';

axios.get('{BASE_URL}/api/v1/transactions/{hash_id}').then((response) {
    console.log(response.data.transaction);
});
```

**Request**

Property | Description
---|---
`include` | A comma separated value of related data. Values allowed are: `buyer`, `seller` and `payment`. Returns `null` if nothing is associated

**Response**

Property | Description
---|---
`success` | true
`data` | The transactions data. Returns an empty array if no data can be found

Fetching a single transaction has two conditions before you can access the resource. These are:  
- If buyer is **NULL**, the transaction can be accessed both publicly and privately
- If buyer is **NOT NULL**, only the seller and buyer can view the resource


### Update
`auth: required`
```endpoint
PUT/PATCH api/v1/transactions/{hash_id}
```

Updates a transaction. Where {hash_id} is the transaction ID. Parameters are optional although if present, it will run validation process.

```javascript
import axios from 'axios';

axios.put('{BASE_URL}/api/v1/transactions/{hash_id}', {
    // This only updates the amount
    amount: 123.45
});
```

**Request**

There are two conditions before updating buyer:
- If buyer is **NULL**, you can update it.
- If buyer is **NOT NULL** (means it already has a buyer), you **CANNOT** update it.

Property | Description
---|---
`buyer` | (Optional) Either Integer or NULL. If Integer, it should be the `User.id` of buyer
`amount` | (Optional) Numeric. Minimum value: 200

**Response**

Property | Description
---|---
`success` | true
`data` | The transactions data. Returns an empty array if no data can be found

### Delete
`auth: required`
```endpoint
DELETE api/v1/transactions/{hash_id}
```

Deletes a transaction. Where {hash_id} is the transaction ID.
This endpoint does not require any request body.

```javascript
import axios from 'axios';

axios.delete('{BASE_URL}/api/v1/transactions/{hash_id}');
```

**Response**

```
{
    "success": true
}
```

## TransactionPayments



### Read Many
`auth:required`
```endpoint
GET api/v1/transaction/payments
```
This fetches all payments made by the user

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

### Read Single
`auth:required`
```endpoint
GET api/v1/transaction/payment/{hash_id}
```

Get a buyer's payment information

**Request**

Property | Description
---|---
`include` | A comma separated value of related data. The only allowed value is `transaction`

**Response**

Property | Description
---|---
`success` | true
`data` | The transactions data. Returns an empty array if no data can be found

## Users

### Fetch One
`auth: required`
```endpoint
GET api/v1/users/{hash_id}
```

This fetches a user's information

```javascript
import axios from 'axios';

axios.delete('{BASE_URL}/api/v1/users/{hash_id}');
```

**Request**

NONE

**Response**

Property | Description
---|---
`success` | true
`data` | The user's data. Returns an empty array if no data can be found
