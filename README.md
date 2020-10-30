# Documentation
To view documentation:

**Step 1**:  
Open terminal and type

```sh
cd docbox
npm install
npm start
```

**Step 2**:  
Open browser and navigate to `http://192.168.0.10:9966`


# TODO
🗨 = To be discussed  
☑️ = Done  
👷‍♂️ = Under Construction    


## Auth
Type | Endpoint | Status
---|---|---
`POST` | /auth | ☑️
`POST` | /auth/logout | ☑️
`POST` | /auth/signup/email | ☑️
`POST` | /auth/user | ☑️
---
## Transactions
Type | Endpoint | Status
---|---|---
`POST` | /transactions/{hash_id} | ☑️
`GET` | /transactions | ☑️
`PUT` | /transactions/{hash_id} | ☑️
`DELETE` | /transactions/{hash_id} | ☑️
---
## TransactionPayments
Type | Endpoint | Status
---|---|---
`GET` | /transaction/payments | ☑️
`GET` | /transaction/payments/{hash_id} | ☑️
---
## Users
Type | Endpoint | Status
---|---|---
`GET` | /users | 🗨
`GET` | /users/{hash_id} | ☑️
---
## Settings
Type | Endpoint | Status
---|---|---
`PUT` | /settings/{field\|s} | 🚧
---
### Relationships
Type | Endpoint | Status
---|---|---
`GET` | /transaction/{hash_id}/payments | 🚧 
`GET` | /transaction/payment/{hash_id}/logs | 🚧 - WIP
`GET` | /user/{hash_id}/transactions | ☑️
---
## Documentation
🚧 **Needs to be updated after finishing endpoints and tests** 🚧
