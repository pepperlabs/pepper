![Test](https://github.com/pepperlabs/pepper/workflows/Test/badge.svg?branch=master)

<a href="https://pepperlabs.github.io/docs/">
    <p align="center">
        <img src="https://raw.githubusercontent.com/pepperlabs/docs/master/static/read-the-documentation-button.png" />
    </p>
</a>

Install via composer:

```bash
composer require pepperlabs/pepper
```

Pepper is an automatic GraphQL exposing Laravel package. It uses your application defined models to auto define properties and relations. It supports Query and Mutation out of the box.

features:

- Customizable validation, authentication, and authorization
- Optional JWT support for protecting routes including login, register, forget password and reset password
- Support all Laravel Eloquent databases (SQLite, MySQL, PostgreSQL, SQLServer)

> Please note that this repo is still a work-in-progress project.

## Version support

PHP: 7.3, 7.4, and 8.0

Laravel: 7.x and 8.x

## Supported databases

As it uses Laravel Eloquent only, it can support all supported Laravel ORM such as: SQLite, MySQL, PostgreSQL and, SQLServer.

If you discover a security vulnerability within Pepper, please send an e-mail to Amirmasoud Sheydaei via [amirmasoud.sheydaei@gmail.com](mailto:amirmasoud.sheydaei@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The MIT License (MIT). Please see [License File](LICENSE) File for more information.
