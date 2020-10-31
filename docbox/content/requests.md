## Requests
If the request body has an input, most of the endpoints will run a validation. The validation goes through Laravel's validation process.  

To know more about it, go here: [AJAX Requests and Validation](https://laravel.com/docs/8.x/validation#quick-ajax-requests-and-validation)


## Responses
A successful API call will be sent with a `200` HTTP status code.

### Validation Errors

All validation error response will be sent with a `422` HTTP status code

Failed validation follows this format:
````javascript
{
    "message": "The given data is invalid",
    "errors": {
        "parameter_name": [
            "The parameter_name is required",
            // More errors will be added within this array
        ]
    }
}
````

Property | Description
---|---
`message` | (String) The generalized description of the errors
`errors`  | (Object) The specific error messages where the `key` is the name of field/parameter and `value` will be an array of all the error messages


### Authentication Errors
For endpoints that require user authentication, a response will be sent with a `401` HTTP Status code IF the user is unathorized and will *always* contain this JSON:

````javascript
{
    "message": "Unauthenticated."
}
````

### Server Related Errors
Such as thrown exceptions. Will return status `500`

```javascript
// Example server related response
{
    "message": "Error details here"
}
```
