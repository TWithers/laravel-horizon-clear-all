# Laravel Horizon Clear All

------

This package provides a convenient way to clear all your Horizon queues in a single command instead of repeating the command multiple times to clear several queues.
Likely this will be used in a development environment, but it can be used in a production environment as well.

## 🚀 Installation

> **Requires [PHP 8.2+](https://php.net/releases), [Laravel 11.0+](https://laravel.com), and [Laravel Horizon 5.0+](https://laravel.com/docs/11.x/horizon)**

You can install the package via [Composer](https://getcomposer.org):

```bash
composer require twithers/laravel-horizon-clear-all
```

## 🙌 Usage

To clear all queues, run the following command:

```bash
php artisan horizon:clear-all
```

If you are running this in a production environment, you will be prompted to confirm the action. You can pass the `--force` flag to skip the confirmation prompt:

```bash
php artisan horizon:clear-all --force
```

**Laravel Horizon Clear All** was created by **[Tim Withers](https://twitter.com/TheTimWithers)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
