# laravel-whois

<p align="center">
    <a href="https://packagist.org/packages/larva/laravel-whois"><img src="https://poser.pugx.org/larva/laravel-whois/v/stable" alt="Stable Version"></a>
    <a href="https://packagist.org/packages/larva/laravel-whois"><img src="https://poser.pugx.org/larva/laravel-whois/downloads" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/larva/laravel-whois"><img src="https://poser.pugx.org/larva/laravel-whois/license" alt="License"></a>
</p>

Laravel 的 Whois 查询模块。


## 环境需求

- PHP >= 8.0.2

## Installation

```bash
composer require larva/laravel-whois -vv
```

```bash
php artisan migrate
```

```php
    $info = \Larva\Whois\Whois::lookup('baidu.com', true);
    $info = \Larva\Whois\Whois::lookupRaw('google.com');
```
