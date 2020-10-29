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
👷 = Under Construction    


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
`GET` | /transaction/payments/{id} | ☑️
---
## TransactionLogs
Type | Endpoint | Status
---|---|---
`GET` | /transaction/{hash_id}/logs | 👷
---
## Users
Type | Endpoint | Status
---|---|---
`GET` | /users | 🗨
`GET` | /users/{hash_id} | 👷
`GET` | /users/{hash_id}/transactions | 🚧
---
## Settings
Type | Endpoint | Status
---|---|---
`PUT` | /settings/{field\|s} | 🚧
---
## Tests
Class | Status
---|---
`tests/Feature/UserReadSingleTest.php` | 🚧
`tests/Feature/UserReadMultipleTest.php` | 🚧
`tests/Feature/TransactionPaymentReadTest.php` | 🚧
`tests/Feature/UserTransactionsReadTest.php` | 🚧
`tests/Feature/TransactionReadMultipleWithIncludeTest.php` | 🚧
`tests/Feature/TransactionReadMultipleWithFilterTest.php` | 🚧
`tests/Feature/TransactionReadMultipleWithSortTest.php` | 🚧
`tests/Feature/PaymentReadMultipleWithIncludeTest.php` | 🚧
`tests/Feature/PaymentReadMultipleWithFilterTest.php` | 🚧
`tests/Feature/PaymentReadMultipleWithSortTest.php` | 🚧
---
## Documentation
🚧 **Needs to be updated after finishing endpoints and tests** 🚧
