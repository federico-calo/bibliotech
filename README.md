# Bibliotech

**Bibliotech** is a book management application that allows users to add, edit, delete, and view books and their associated information, such as authors, categories, and summaries.

## Table of Contents
1. [Features](#features)
2. [Installation](#installation)
3. [Configuration](#configuration)

---

## Features
- **Add Books**: Create a new book record by providing the title, author, ISBN, a summary, and associated tags/categories.
- **Edit Books**: Update the existing information of a book.
- **Delete Books**: Delete a book from the database.
- **Browse Books**: View the details of a book, including associated categories and tags.
- **User Authentication**: Access edit and delete features after login.

## Installation

### Prerequisites
- **PHP** version 8.1 or higher
- **MySQL** version 8 or higher

### Installation steps

1. **Clone repository :**
   ```bash
   git clone https://github.com/federico-calo/bibliotech.git
   cd bibliotech

2. **Install PHP dependencies:**

composer install

3. **Configuration**

Edit the settings.php file with your database information:

$settings = [];
$settings['db'] = 'mysql:host=localhost;dbname=database_name';
$settings['mysql_user'] = 'mysql_username';
$settings['mysql_password'] = 'mysql_password';

4. ** Manage data **

Import database tables
`composer site:install`

Optionnaly, import demo data
`composer demo:install`

5. **Clean and test code**

Clean up the code with rector and phpcs
`vendor/bin/rector process web --dry-run`
`vendor/bin/rector process web`
`vendor/bin/phpcs web`
`vendor/bin/phpcbf web`

Check standards with PHPStan
`vendor/bin/phpstan web analysis`

Test with PHPUnit
`vendor/bin/phpunit --color`