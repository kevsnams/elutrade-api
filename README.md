# Introduction
Run `php artisan db:seed` if you want to fill the database with dummy data

# API Endpoints

----

#### NOTE: A successful API call will be sent with a 200 HTTP status code.

----

## Signup and Authentication
### `POST` **api/v1/auth**
##### Request Parameters

| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| email | *String* | Required, Max Length: 255 |
| device_name | *String* | Required, Max Length: 255 |
| password | *String* | Required |

##### Response
/!\\ NOTE /!\\: If this fails on validation, the message will only appear on 'email' parameter
```json
{
	"success": true,
	"access_token": "soMe_ToK3n_$tR1ng_h3rE"
}
```

### `GET` **api/v1/auth/user**
Fetches the current authenticated user. Must be authenticated to fetch data.
##### Request Parameters
No Request Parameters

##### Response
```javascript
{
    "success": true,
    "user": {
        /* user info here */
    }
}
```

### `POST` api/v1/signup/email
For email signup

##### Request Parameters

| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| email | *String* | Required, Max Length:255 |
| first_name | *String* | Required, Max Length: 255 |
| middle_name | *String* | Optional. Max Length: 255 |
| last_name | *String* | Required, Max Length: 255 |
| password | *String* | Required |
| password_confirm | *String* | Required, Same: password |
| accepted_terms | Boolean | Required |

##### Response
```json
{
	"success": true
}
```

### `POST` api/v1/signup/confirm
##### Request Parameters
| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| confirmation_code | *String* | Required, TBD |

##### Response
```json
{
	"success": true
}
```

## Transactions
### `DELETE` api/v1/transactions/{id}
Deletes a transaction. Where {id} is the transaction ID. *Requires authorization to update*.

##### Request Parameters
*No Request Parameters*/

##### Response
```javascript
{
    "success": true
}
```

### `POST` api/v1/transactions
Creates a transaction. Must be authenticated to create a transaction.

##### Request Parameters
| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| buyer | Integer/Null | Should be present but can be null (no buyer). If `buyer` is an Integer, then it will look for the User.id |
| amount | Numeric | Required, Numeric (e.g. 200/200.69), Min: 200 |

##### Response
```json
{
    "success": true,
    "transaction": {
        "id": "TRANSACTION_ID",
        "seller": "JSON_OBJECT_THAT_CONTAINS_SELLER_INFORMATION",
        "buyer": "NULL_OR_JSON_OBJECT_THAT_CONTAINS_BUYER_INFORMATION",
        "amount": "TRANSACTION_AMOUNT",
        "status": "TRANSACTION_STATUS_CODE",
        "created_at": "WHEN_THE_TRANSACTION_IS_CREATED",
        "updated_at": "WHEN_THE_TRANSACTION_WAS_LAST_UPDATED"
    }
}
```

### `PUT` api/v1/transactions/{id}
Updates a transaction. Where {id} is the transaction ID. *Requires authorization to update*.
Parameters are optional although if present, it will run validation process.

##### Request Parameters
There are two conditions before updating `buyer`:
1. If `buyer` is NULL, you can update it.
2. If `buyer` is NOT NULL (means it already has a buyer), you CANNOT update it.

| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| buyer | Null/Integer | Optional. If Integer, it should be the user id of buyer |
| amount | Numeric | Optional. Min: 200 |

##### Response
It's the same as `GET api/v1/transactions/{id}` but returns the updated values

```javascript
{
    "success": true,
    "transaction": {
        /** null or transaction details (see "api/v1/transactions" endpoint for more details)  */
    }
}
```

### `GET` api/v1/transactions
Fetches all transactions of seller. Must be authenticated to return resources.


##### Request Parameters
| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| per_page | Integer | Optional. Number of transactions to display per page |
| with | Array | Default: 'buyer'. Available: ['buyer', 'seller'] |

##### Response
By default, parameter `with` will be set to "buyer" and will be appended to each transaction. You can also use "seller" or use both.

```javascript
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
            },
            {
                "id": 32,
                "amount": "391601.00",
                "status": 0,
                "created_at": "2020-10-01T14:11:30.000000Z",
                "updated_at": "2020-10-01T14:11:30.000000Z",
                "buyer": {
                    "id": 47,
                    "email": "shanahan.paul@example.net",
                    "first_name": "Sierra",
                    "middle_name": "Brekke",
                    "last_name": "Grady"
                }
            },
            /** . . . */
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

### `GET` api/v1/transactions/{id}
Fetches a single transaction by its {id}

##### Request Parameters
No Request Parameters

##### Response
Fetching a single transaction has two conditions before you can access the resource. These are:
- If buyer is null, the transaction can be accessed both publicly and privately
- If buyer is NOT NULL, only the seller and buyer can view the resource

```javascript
{
    "success": true,
    "transaction": {
        /** null or transaction details (see "api/v1/transactions" endpoint for more details)  */
    }
}
```
   
# Validation Errors
All API endpoints goes through a validation process where all inputs are being checked.

----

#### NOTE: This JSON response will be sent with a 422 HTTP status code.

----

The Response JSON of a failed validation follows this format:

```json
{
	"message": "The given data is invalid.",
	"errors": {
		"parameter_name": [
			"The parameter_name is required"
		]
	}
}
```

`message`
- The message that describe the errors as a whole

`errors`
- Will contain a  { `key`: `value` } pair where:

  - `key` is the parameter name
  - `value` is an Array containing all the specific error messages
  
# Unathenticated Users
For endpoints that require user authentication, a response will be sent with a 401 HTTP Status code IF the user is unathorized and will always contain this JSON:

```json
{
    "message": "Unauthenticated."
}
```
