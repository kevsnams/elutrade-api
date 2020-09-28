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

### `POST` api/v1/signup
##### Request Parameters

| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| email | *String* | Required, Max Length:255 |
| first_name | *String* | Required, Max Length: 255 |
| middle_name | *String* | Required, Max Length: 255 |
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
### `POST` api/v1/transactions
Creates a transaction. Must be authenticated to create a transaction.

##### Request Parameters
| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| buyer | Integer/Null | Should be present but can be null (no buyer). If `buyer` is an Integer, then it will look for the User.id |
| amount | Numeric | Required, Numeric (e.g. 200/200.69) 

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
For endpoints that require user authentication, a response will be sent with a 401 HTTP Status code and will always contain this JSON:

```json
{
    "message": "Unauthenticated."
}
```
