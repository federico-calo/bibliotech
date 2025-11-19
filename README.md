# Bibliotech

Bibliotech is a lightweight book management application that lets you create, edit, browse, and delete books along with their associated metadata (authors, categories, tags, summaries, etc.). Authenticated users gain access to management features.

---

## Table of Contents
1. [Features](#features)
2. [Stack](#stack)
3. [Quick Start (Docker)](#quick-start-docker)
4. [Manual Installation](#manual-installation)
5. [Configuration](#configuration)
6. [Database & Demo Data](#database--demo-data)
7. [Code Quality & Tests](#code-quality--tests)
8. [Front Assets](#front-assets)
9. [Project Structure](#project-structure)
10. [Contributing](#contributing)
11. [License](#license)

---

## Features
- **Add books** with title, author, ISBN, summary, and tags.
- **Edit and delete** existing records.
- **Browse** detailed book listings, categories, and tags.
- **Export / import** data via CSV helpers.
- **User authentication** for protected actions.

## Stack
- PHP 8.3 (compatible with PHP ≥ 8.1)
- MySQL 8
- Redis 7 (for session and cache helpers)
- Apache 2
- Composer for dependency management

## Quick Start (Docker)

The Docker setup spins up PHP/Apache, MySQL, and Redis containers, and seeds the database automatically with the default schema and sample data.

```bash
make init
```

or, manually

```bash
cp settings.php.dist settings.php        # seulement la première fois
cp env.example .env                      # adapter les ports/identifiants si besoin
docker compose up --build -d
```

- Application: http://localhost:8080 (override avec `APP_PORT` dans `.env`)
- MySQL: `localhost:3307` (`lamp` / `lamp`; root password `root`)
- Redis: `localhost:6380`

Useful commands:

```bash
# Scripts Composer dans le conteneur PHP
make composer-site:install
make composer-demo:install

# Shell interactif / logs
make shell
make logs

# Tout arrêter (et supprimer les volumes si besoin)
docker compose down --volumes
```
- `composer site:install` runs `scripts/install.php`, which ensures the database schema and baseline data.
- `composer demo:install` (optional) executes `scripts/install_demo.php` to load sample books/tags.
- SQL fixtures live in `./data/` if you prefer importing manually (`schema.sql`, `data.sql`, `reset*.sql`).
- Bootstrap assets are installed automatically inside the container (see [Front assets](#front-assets)).

## Manual Installation

### Prerequisites
- PHP 8.1+
- MySQL 8+
- Redis 7 (optional but recommended)
- Composer

### Steps

```bash
git clone https://github.com/federico-calo/bibliotech.git
cd bibliotech
cp settings.php.dist settings.php
composer install
```

Adjust `settings.php` to point to your local services (see [Configuration](#configuration)), then create the database and run:

```bash
composer site:install      # creates tables and base data
composer demo:install      # optional: loads demo catalog
```

Finally, point your web server (Apache/Nginx) to the `web/` directory as the document root.

## Configuration

All runtime options live in `settings.php`. Key entries:

```php
$settings['db'] = 'mysql:host=database;dbname=lamp'; // DSN string
$settings['mysqlUser'] = 'lamp';                     // MySQL username
$settings['mysqlPassword'] = 'lamp';                 // MySQL password
$settings['redisHost'] = 'cache';                    // Redis hostname
```

- For Docker, the defaults match the compose services (`database`, `cache`, `lamp` credentials).
- For a local setup, update the DSN and credentials accordingly.
- To enable debug mode, set `$settings['debug'] = true;`.

## Database & Demo Data
- `composer site:install` runs `scripts/install.php`, which ensures the database schema and baseline data.
- `composer demo:install` (optional) executes `scripts/install_demo.php` to load sample books/tags.
- SQL fixtures live in `./data/` if you prefer importing manually (`schema.sql`, `data.sql`, `reset*.sql`).

## Code Quality & Tests

```bash
# Static analysis and formatting
vendor/bin/rector process web --dry-run
vendor/bin/rector process web
vendor/bin/phpcs web
vendor/bin/phpcbf web

# Static analysis (correct command syntax)
vendor/bin/phpstan analyse web

# Unit tests
vendor/bin/phpunit --color
```

Via Docker : `make rector`, `make lint`, `make phpstan`, `make phpunit` (ou `docker compose exec app <commande>`).

## Front assets

- On first startup, the container installs twbs/bootstrap via Composer if needed (which updates composer.json/composer.lock) and automatically copies bootstrap.min.css and bootstrap.bundle.min.js to web/assets/.
- Font Awesome (version configurable via FONT_AWESOME_VERSION) is downloaded from GitHub, with its CSS/WEBFONTS copied to web/assets/font/fontawesome. The layout loads /assets/font/fontawesome/css/all.min.css, making icons immediately available offline.
- The style sheets/JS point to /assets/... so that Bootstrap and Font Awesome are immediately available, regardless of the depth of the rendered view.
- To force an update:

```bash
docker compose exec app composer require twbs/bootstrap:^5.3
docker compose exec app cp vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js web/assets/js/bootstrap.bundle.min.js
docker compose exec app cp vendor/twbs/bootstrap/dist/css/bootstrap.min.css web/assets/css/bootstrap.min.css
```

- To refresh Font Awesome, delete the `web/assets/font/fontawesome` directory and then restart the container (`docker compose restart app`).

## Project Structure

```
web/                # Application entrypoint, controllers, entities, templates
scripts/            # CLI helpers (install, reset, import)
data/               # SQL fixtures and CSV imports
docker/             # Docker build assets
```

## Contributing
1. Fork the repository and create a feature branch.
2. Run linters/tests before submitting a PR.
3. Describe your changes clearly and link related issues.

## License

Distributed under the MIT License. See `LICENSE` for details.