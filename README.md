# Laravel Bakery

## Installation
```
composer require simklee/laravel-bakery --dev
```

## Publish package files
The install command copies all necessary classes and files.
```
php artisan bake:install
```

## Commands
### BakeModelCommand
#### Arguments
* **model** (optional) Name of the model.
#### Options
* **--all** : Generate all models in config file.
* **--config=** : Define the config file name (without file extension .php).
* **--sample** : Create a sample config file.

#### Examples
Creates a config file with a sample:
```
php artisan bake:model --sample
```
Create the model with name ModelName with the settings in the config file:
```
php artisan bake:model ModelName
```

