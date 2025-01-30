# Contributing to Bibliotech

Thank you for considering contributing to **Bibliotech**! Contributions are what make open-source projects thrive. Whether you're reporting a bug, suggesting an improvement, or submitting a pull request, we appreciate your effort and time.

## Table of Contents
1. [Getting Started](#getting-started)
2. [How to Contribute](#how-to-contribute)
3. [Code of Conduct](#code-of-conduct)
4. [Reporting Bugs](#reporting-bugs)
5. [Suggesting Features](#suggesting-features)
6. [Submitting Changes](#submitting-changes)
7. [Style Guide](#style-guide)

---

## Getting Started
1. Fork the repository to your own GitHub account.
2. Clone the forked repository to your local machine:
   ```bash
   git clone https://github.com/federico-calo/bibliotech.git
   cd bibliotech
   ```
3. Install the dependencies:
   ```bash
   composer install
   ```
   See README.md file for next installation steps.

## How to Contribute
There are several ways to contribute:
- Fix typos or improve documentation.
- Report bugs or suggest new features by opening an issue.
- Submit pull requests for enhancements, bug fixes, or new features.

## Code of Conduct
Please follow our [Code of Conduct](CODE_OF_CONDUCT.md) to create a welcoming and inclusive environment.

## Reporting Bugs
If you find a bug, please create an issue with the following details:
- **Description:** A clear and concise description of the problem.
- **Steps to Reproduce:** Detailed steps to reproduce the issue.
- **Expected Behavior:** What you expected to happen.
- **Actual Behavior:** What actually happened.
- **Environment:** Information about your setup (e.g., PHP version, MySQL version, etc.).

## Suggesting Features
We welcome ideas for new features! When suggesting a feature, include:
- **Description:** What you want to achieve.
- **Use Case:** Why this feature would be useful.
- **Possible Implementation:** If you have ideas on how to implement it, feel free to share.

## Submitting Changes
1. Create a new branch for your changes:
   ```bash
   git checkout -b feature-or-bugfix-name
   ```
2. Make your changes.
3. Test your changes thoroughly.
4. Commit your changes with a meaningful commit message:
   ```bash
   git commit -m "Description of the changes made"
   ```
5. Push your branch to GitHub:
   ```bash
   git push origin feature-or-bugfix-name
   ```
6. Open a pull request on the main repository, providing a clear description of your changes.

## Style Guide
To ensure consistency, please follow these guidelines:
- **Code Formatting:** Use [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- **Linting:** Run the following commands before submitting your code:
   ```bash
   vendor/bin/phpcs web
   vendor/bin/phpstan analyse web
   ```
- **Testing:** Add or update PHPUnit tests when necessary and ensure all tests pass:
   ```bash
   vendor/bin/phpunit --color
   ```

Thank you for your contributions and support! 🚀

