# Suqucom Project

## Prerequisites

Before running the project, ensure that you have the following installed:

- [PHP](https://www.php.net/downloads) (version 8.0 or higher)
- [Composer](https://getcomposer.org/download/)
- [MySQL](https://dev.mysql.com/downloads/mysql/) or any other supported database
- [Node.js](https://nodejs.org/en/download/) (for Laravel Mix)
- [Git](https://git-scm.com/downloads)

## Getting Started

### 1. Clone the Repository

Clone the repository from GitHub:

```bash
git clone https://github.com/Alnuzaili/Suqucom-eCommerce-System.git
cd Suqucom-eCommerce-System
```

### 2. Install Dependencies

Install the PHP and Node.js dependencies using Composer and npm:

```bash
composer install
```

### 3. Environment Configuration

Copy the .env.example file to .env:

```bash
cp .env.example .env
```

Open the .env file and update the following variables:

- DB_DATABASE: The name of your database.
- DB_USERNAME: Your database username.
- DB_PASSWORD: Your database password.

You may also need to configure other environment variables like APP_URL and MAIL_* settings.


### 4. Generate Application Key

Generate a new application key:

```bash
php artisan key:generate
```

### 5. Run Migrations and Seed Database

Run the migrations to create the database tables:

```bash
php artisan migrate
php artisan db:seed
```

You can also run a specific seeder using this command:

```bash
php artisan db:seed --class=ClassName
```

### 6. Install and Configure Passport

Run the following command to install Passport:

```bash
php artisan passport:install
php artisan passport:client
php artisan passport:client --personal
```

### 7. Run the Application

Start the Laravel development server:

```bash
php artisan serve
```

## Testing the API

You can use tools like Postman to test the API endpoints. Make sure to include the Authorization header with the bearer token obtained from Passport.

## Contributing

If you'd like to contribute to this project, feel free to fork the repository and submit a pull request.

