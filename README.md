# Load blade views from AWS S3

You can use RemoteView::make('some_view)->render(); to load blade files from S3.
You need to have the s3 disk configured.
Also is a new blade directive called @remoteInclude('some_view') to use in your views.

## Installation

You can install the package via composer:

```bash
composer require wehaa/remote-view
```

## Usage

``` php
RemoteView::make('some_view)->render();
```

or @remoteInclude('layouts.partials.body-start-scripts') in your blade files.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email sassaroli@gmail.com instead of using the issue tracker.

## Credits

- [Mauro Sassaroli](https://github.com/wehaa)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).