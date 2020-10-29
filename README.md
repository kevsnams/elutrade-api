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
## Auth
Endpoint | Status
---|---
`POST` /auth | ☑️
`POST` /auth/logout | ☑️
`POST` /auth/signup/email | ☑️
`POST` /auth/user | ☑️
---
## Transactions
Endpoint | Status
---|---
`POST` /transactions/{hash_id} | ☑️
`GET` /transactions | ☑️
`PUT` /transactions/{hash_id} | ☑️
`DELETE` /transactions/{hash_id} | ☑️
`GET` /transactions/{hash_id}/payment | 👷
---
## TransactionPayments
Endpoint | Status
---|---
`GET` /transaction/payments | ☑️
`GET` /transactions/payments/{id} | ☑️
---
## Users
Endpoint | Status
---|---
`GET` /users | 🗨
`GET` /users/{hash_id} | 🚧
`GET` /users/{hash_id}/transactions | 🚧
---
## Settings
Endpoint | Status
---|---
`PUT` /settings/{field\|s} | 🚧
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
