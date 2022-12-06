
# Application status checker

By Erfan Ebrahimi

## Run Localy

- Download project form Git

```bash
  git clone https://github.com/yeganehha/subscription-status subscription-status
  cd subscription-status
  composer install 
```
- Then open `.env` file and config database and mail server

- Then
```bash
  php artisan migrate
  php artisan db:seed
  php artisan serve
```
- For setting email configuration and choose day for run checking, edit `config/subscription.php`

- Then open `[http://127.0.0.1:8000]`


## FAQ

#### 1. How To Add New Platform?

For adding new platform provider just need to call:

```
    PHP artisan make:platform <name>
```

#### 2. How To Run Checking Subscription?

For Checking Subscription manually:

```
PHP artisan queue:listen
PHP artisan subscription:check
```
also you can add These cronjobs to server to check subscription automatically:
```
* * * * * /usr/local/bin/php /laravel/artisan queue:work --stop-when-empty
0 0 * * * /usr/local/bin/php /laravel/artisan subscription:check
```




## Running Tests

To run tests, run the following command

```bash
  php artisan test
```

