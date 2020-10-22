## Installation
If this API is a fresh clone, then you need to execute `composer install`  
Otherwise, just keep in mind to run `composer update` if there are changes made to the dependencies.  
  
  
Once you have the dependencies installed, run:  
`php artisan serve`  

## Database Seeding
To run the seeder, run:
`php artisan db:seed`  

This will create users with associated transactions.
All seeded users will have a password: `password`


## Server
You can use `php artisan serve` or clone this repo to your apache server.  

## Changelog

- Transaction `id` is no longer used. Use `hash_id` from now on.
