
# Snayvik translation for laravel 8

The simplest laravel translation based on files and database syncing. It will import all your translations from project's "lang" directory to database and from database to translation fileswith a single click.

## Screenshots

![App Screenshot](https://via.placeholder.com/468x300?text=App+Screenshot+Here)

## Installation

Install Snayvik translation using composer

```bash
  composer require snayvik/translation
```

Run Migration

```bash
  php artisan migrate
```

## Configuration

To override the defualt configuration, you have to pulish the snayvik translation file into your code using :

```
php artisan vendor:publish --tag=snayvik-translation-config
```

### Options :

``` route_prefix ``` : Prefix for translation route. If route prefix is empty then translations page will be shown on yourdomain/translations.

``` route_middleware ``` : Will be an array. The default is web and auth.

``` extend_blade ``` : The blade file is using template inheritance. So you may have to change the extendable blade file. The default is 'layouts.app' which is provided by laravel itself.

``` content_section ``` : The html content section where translation setting will be rendered. Default is content.

``` javascript_section ``` : The translations key value data will be saved using XMLHttpRequest and using javascript.So you must have to add it. Default is js.