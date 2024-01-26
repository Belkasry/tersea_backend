## Prerequisites

- [Composer](https://getcomposer.org)  installed on your system.

## Step 1: Clone the Repository

```bash
git clone <repository-url>
cd <repository-directory>
```
## Step 2: composer
```bash
composer install
```
## Step 3: Copy .env.example to .env
After copying, edit the .env file:
- Configure your MySQL database details under the DB_* settings.
- Set up SMTP settings for mail functionality under the MAIL_* settings.
```bash
cp .env.example .env
```
## Step 4: Generate Application Key
```bash
php artisan key:generate
```


## Step 5: Run Migrations, Seeders 
```bash
php artisan migrate:fresh --seed 
```


