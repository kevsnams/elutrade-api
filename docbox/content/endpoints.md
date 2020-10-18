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

```javascript
import axios from 'axios';

axios.post('{BASE_URL}/api/v1/auth', {
    email: 'kevin@example.com',
    password: 'password',
    device_name: '{DEVICE_NAME}'
});
```

**Response**

```
{
    "success": true,
    "access_token": "1|s0m3_t0KeN_$tr1nG_h3Re"
}
```

This will be required whenever you make a request to authenticated endpoints. Add the response `access_token` to `Authorization` header. 

```
{
    "Authorization": "Bearer {access_token}"
}
```

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
This endpoint does not require any request body

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
```
{
    "success": true
}
```

## AuthUser
`auth: required`
```endpoint
POST api/v1/auth/user
```
Fetches the current authenticated user.  
This endpoint does not require any request body
```javascript
import axios from 'axios';

axios.post('{BASE_URL}/api/v1/auth/user)
```

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
```
{
    "success": true,
    "transaction": {
        /**
         * The transaction information.
         * This also has a `seller` and `buyer` property which fetches
         * the seller and buyer information respectively
         */
    }
}
```

### Read All
`auth: required`
```endpoint
GET api/v1/transactions
```
Fetches all transactions of the seller. The response JSON is paginated.

**Request**

Property | Description
---|---
`per_page` | (Optional) Number of transactions to return per page. Default: 10
`with` | (Optional) This property adds a property on transactions. Available values are 'buyer' and 'seller'. Default: \['buyer'\]

```javascript
import axios from 'axios';

const qs = required('query-string');
const props = qs.stringify({
    per_page: 10,
    // Includes 'buyer' and 'seller' to the result set
    with: ['buyer', 'seller']
});

axios.get(`{BASE_URL}/api/v1/transactions${props}`);
```

**Response**

By default, parameter with will be set to `buyer` and will be appended to each transaction. You can also use `seller` or both.

```
{
    "success": true,
    "transactions": {
        "current_page": 1,
        "data": [
            {
                "id": 30,
                "amount": "920686.00",
                "status": 0,
                "created_at": "2020-10-01T14:11:30.000000Z",
                "updated_at": "2020-10-01T14:11:30.000000Z",
                "buyer": {
                    "id": 45,
                    "email": "hhodkiewicz@example.com",
                    "first_name": "Ellie",
                    "middle_name": "Ullrich",
                    "last_name": "Ledner"
                }
            },
            {
                "id": 31,
                "amount": "697644.00",
                "status": 0,
                "created_at": "2020-10-01T14:11:30.000000Z",
                "updated_at": "2020-10-01T14:11:30.000000Z",
                "buyer": {
                    "id": 46,
                    "email": "ludie87@example.com",
                    "first_name": "Aurelia",
                    "middle_name": "Schmitt",
                    "last_name": "Rogahn"
                }

                // Adds "seller" if specified on 'with' property
            }

            // More results...

        ],
        "first_page_url": "http://127.0.0.1:8000/api/v1/transactions?page=1",
        "from": 1,
        "last_page": 3,
        "last_page_url": "http://127.0.0.1:8000/api/v1/transactions?page=3",
        "links": [
            {
                "url": null,
                "label": "Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/transactions?page=1",
                "label": 1,
                "active": true
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/transactions?page=2",
                "label": 2,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/transactions?page=3",
                "label": 3,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/transactions?page=2",
                "label": "Next",
                "active": false
            }
        ],
        "next_page_url": "http://127.0.0.1:8000/api/v1/transactions?page=2",
        "path": "http://127.0.0.1:8000/api/v1/transactions",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 30
    }
}
```

### Read Single
```endpoint
GET apit/v1/transactions/{id}
```

Fetches a single transaction by its {id}  
This endpoint does not require any request body

```javascript
import axios from 'axios';

axios.get('{BASE_URL}/api/v1/transactions/1234').then((response) {
    console.log(response.data.transaction);
});
```

**Response**

Fetching a single transaction has two conditions before you can access the resource. These are:  
- If buyer is **NULL**, the transaction can be accessed both publicly and privately
- If buyer is **NOT NULL**, only the seller and buyer can view the resource

```
{
    "success": true,
    "transaction": {
        // NULL if transaction does not exist
        // Object if exists
    }
}
```
### Update
`auth: required`
```endpoint
PUT/PATCH api/v1/transactions/{id}
```

Updates a transaction. Where {id} is the transaction ID. Parameters are optional although if present, it will run validation process.

```javascript
import axios from 'axios';

axios.put('{BASE_URL}/api/v1/transactions/{id}', {
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

```
{
    "success": true,
    "transaction": {
        // Updated transaction properties
    }
}
```

### Delete
`auth: required`
```endpoint
DELETE api/v1/transactions/{id}
```

Deletes a transaction. Where {id} is the transaction ID.
This endpoint does not require any request body.

```javascript
import axios from 'axios';

axios.delete('{BASE_URL}/api/v1/transactions/{id}');
```

**Response**

```
{
    "success": true
}
```
