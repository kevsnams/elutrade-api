# API Endpoints

----

#### NOTE: A successful API call will be sent with a 200 HTTP status code.

----
### `POST` **api/v1/auth**
##### Request Parameters

| Parameter | Data Type ||
| ------------ | ------------ | ------------ |
| email | *String* | Max Length: 255 |
| password | *String* |

##### Response
```json
{
	success: true,
	auth_token: "soMe_ToK3n_$tR1ng_h3rE"
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
	success: true
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
	success: true
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
