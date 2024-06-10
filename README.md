# Yii2 Console Application for XML Data Import

This Yii2 console application is designed to parse an XML file and save the data into a MySQL database. The application uses Yii2's ActiveRecord for database interactions, ensuring a simple and robust implementation.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



## Requirements

- PHP 7.2 or higher
- Composer
- MySQL 5.6 or higher

## Installation

Follow these steps to set up and run the application:

### 1. Clone the Repository

~~~
git clone https://github.com/lucasinu/feed-console-app.git
cd feed-console-app
~~~

### 2. Install all the dipendencies

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

~~~
composer install
~~~

### 3. Database

Use the `feed.sql` file in "web/files" to create the database.

Edit the file `config/db.php` with real credentials, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=feed',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

### 4. Running

To run the importing script, locate into the app folder and run:

~~~
php yii import-xml/import
~~~


