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
`GET` | /transaction/payments/{id} | ☑️
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
## Tests
Class | Description | Status
---|---
`tests/Feature/UserReadSingleTest.php` | Create test | ☑️
`tests/Feature/TransactionReadSingleTest.php` | Add test for includes | 👷‍♂️
`tests/Feature/TransactionReadMultipleWithIncludeTest.php` | Create test | 🚧
`tests/Feature/TransactionReadMultipleWithFilterTest.php` | Create test | 🚧
`tests/Feature/TransactionReadMultipleWithSortTest.php` | Create test | 🚧
`tests/Feature/PaymentReadMultipleWithIncludeTest.php` | Create test | 🚧
`tests/Feature/PaymentReadMultipleWithFilterTest.php` | Create test | 🚧
`tests/Feature/PaymentReadMultipleWithSortTest.php` | Create test | 🚧
---
## Documentation
🚧 **Needs to be updated after finishing endpoints and tests** 🚧
