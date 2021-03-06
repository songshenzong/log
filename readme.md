[![Total Downloads](https://poser.pugx.org/songshenzong/log/d/total.svg)](https://packagist.org/packages/songshenzong/log)
[![Latest Stable Version](https://poser.pugx.org/songshenzong/log/v/stable.svg)](https://packagist.org/packages/songshenzong/log)
[![License](https://poser.pugx.org/songshenzong/log/license.svg)](https://packagist.org/packages/songshenzong/log)
[![PHP Version](https://img.shields.io/packagist/php-v/songshenzong/log.svg)](https://packagist.org/packages/songshenzong/log)


## About

Log Request & Debug for Laravel

## Installation

Require this package with composer:

```shell
composer require songshenzong/log
```


## Laravel

Publish configuration files. If not, They can not be serialized correctly when you execute the `config:cache` Artisan command.

```shell
php artisan vendor:publish --provider="Songshenzong\Log\ServiceProvider"
```


## Middleware

If you use a `dingo/api` route, make sure you load the Middleware in `config/api.php`.

```php
'middleware' => [
    'Songshenzong\Log\Middleware',
],
```





The profiler is enabled in all environment by default, You can override that in the config (`songshenzong-log.env`).



Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="Songshenzong\Log\ServiceProvider"
```


## Let's start
```
http://your.domain/songshenzong
```

## Usage

You can now add messages using the Facade (when added), using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

```php
Songshenzong::info($object);
Songshenzong::error('Error!');
Songshenzong::warning('Watch out…');
Songshenzong::addMessage('Another message', 'myLabel');
```

And start/stop timing:

```php
Songshenzong::startMeasure('render','Time for rendering');
Songshenzong::stopMeasure('render');
Songshenzong::addMeasure('now', LARAVEL_START, microtime(true));
Songshenzong::measure('My long operation', function() {
    // Do something…
});
```

Or log exceptions:

```php
try {
    throw new Exception('foobar');
} catch (Exception $e) {
    songshenzongLog() -> addThrowable($e);
}
```

There are also helper functions available for the most common calls:

```php
// All arguments will be dumped as a debug message
debug($var1, $someString, $intValue, $object);

start_measure('render','Time for rendering');
stop_measure('render');
add_measure('now', LARAVEL_START, microtime(true));
measure('My long operation', function() {
    // Do something…
});
```

If you want you can add your own DataCollectors, through the Container or the Facade:

```php
songshenzongLog() -> addCollector(new Songshenzong\Log\DataCollector\MessagesCollector('my_messages'));
//Or via the App container:
$songshenzong_log = App::make('SongshenzongLog');
$songshenzong_log->addCollector(new Songshenzong\Log\DataCollector\MessagesCollector('my_messages'));
```



Note: Not using the auto-inject, will disable the Request information, because that is added After the response.
You can add the default_request data collector in the config as alternative.

## Enabling/Disabling on run time
You can enable or disable the Songshenzong during run time.

```php
songshenzongLog() -> enable();
songshenzongLog() -> disable();
```

NB. Once enabled, the collectors are added (and could produce extra overhead), so if you want to use the Songshenzong in production, disable in the config and only enable when needed.



## Documentation

Please refer to our extensive [Wiki documentation](https://github.com/songshenzong/log/wiki) for more information.


## Support

For answers you may not find in the Wiki, avoid posting issues. Feel free to ask for support on Songshenzong.com


## License

This package is licensed under the [BSD 3-Clause license](http://opensource.org/licenses/BSD-3-Clause).
