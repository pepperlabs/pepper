![Test](https://github.com/pepperlabs/pepper/workflows/Test/badge.svg?branch=master)

# Pepper

Pepper is a Laravel package that can expose GraphQL endpoint for your defined models with zero configuration.

## Table of contents

- [Pepper](#pepper)
  - [Table of contents](#table-of-contents)
  - [Introducation](#introducation)
    - [Version support](#version-support)
  - [Installation](#installation)
    - [Supported databases](#supported-databases)
  - [Background](#background)
  - [Commands](#commands)
    - [Generate Pepper GraphQL classes](#generate-pepper-graphql-classes)
  - [API](#api)
    - [Query](#query)
      - [`query` syntax](#query-syntax)
      - [`query_by_pk` syntax](#query_by_pk-syntax)
      - [Simple Object](#simple-object)
      - [Aggregate object](#aggregate-object)
      - [Arguments](#arguments)
        - [Distinct](#distinct)
        - [Where](#where)
          - [_and](#_and)
          - [_or](#_or)
          - [_not](#_not)
          - [Operators](#operators)
        - [Order](#order)
        - [Pagination](#pagination)
    - [Mutation](#mutation)
      - [insert](#insert)
      - [insert_one](#insert_one)
      - [update_by_pk](#update_by_pk)
      - [update](#update)
      - [delete_by_pk](#delete_by_pk)
      - [delete](#delete)
    - [Subscription](#subscription)
  - [Authorization](#authorization)
    - [Override authorize](#override-authorize)
    - [override authorization message](#override-authorization-message)
  - [Authentication](#authentication)
  - [Privacy](#privacy)
  - [Customization](#customization)
    - [Override `count` method](#override-count-method)
    - [Override `avg` method](#override-avg-method)
    - [Override `sum` method](#override-sum-method)
    - [Override `max` method](#override-max-method)
    - [Override `min` method](#override-min-method)
    - [Override `description`](#override-description)
  - [Authentication](#authentication-1)
    - [Login](#login)
      - [Override login args](#override-login-args)
    - [Register](#register)
  - [Optimization](#optimization)
  - [Roadmap](#roadmap)
  - [License](#license)

## Introducation

[Table of contents](#table-of-contents)

Pepper is an auto generative GraphQL based on [Laravel wrapper for Facebook's GraphQL](https://github.com/rebing/graphql-laravel).
The goal is simplify and fasten development of GraphQL based APIs.

### Version support

PHP: 7.1.3 or higher

Laravel: 5.6 or higher

## Installation

You can install using [composer](https://getcomposer.org/) from [Packagist](https://packagist.org/packages/pepperlabs/pepper).

```bash
composer require pepperlabs/pepper
```

Initial the base GraphQL classes:

```bash
php artisan pepper:grind --all
```

Add `pepper` middleware to graphql config file.

### Supported databases

As it uses Laravel Eloquent only, it would support SQLite, MySQL, PostgreSQL and, SQLServer.

## Background

[Table of contents](#table-of-contents)

The idea of the extension came from other open source project called [Hasura](https://hasura.io/). Hasura only supports PostgreSQL and in this projects, Pepper
we are supporting MySQL, PostgreSQL, SQLite and SQL Server. Although most of the queries and mutation structure are similar there is no guarantee to be equal.

## Commands

[Table of contents](#table-of-contents)

### Generate Pepper GraphQL classes

Run following command to generate Pepper GraphQL class for interactively:

```bash
php artisan pepper:grind
```

or supply `--all` option to generate for all models.

## API

### Query

#### `query` syntax

[Table of contents](#table-of-contents)

```graphql
query [<op-name>] {
  object [([argument])]{
    object-fields
  }
}
```

Example query:

```graphql
query {
  user(
    where: { name: { _eq: "Amirmasoud" } }
  ) {
    id
    name
    created_at
  }
}
```

#### `query_by_pk` syntax

[Table of contents](#table-of-contents)

Return one model object (if found any) using defined PK defined for model using
[`getKeyName`](https://laravel.com/api/5.8/Illuminate/Database/Eloquent/Model.html#method_getKeyName) which gets `primaryKey` property on the model. It's
usually `id`. Input argument is also limited to this key.

```graphql
query [<op-name>] {
  <query-field-name> (
    column1: value1
  )
  <object-fields>
}
```

Example:

```graphql
query {
  user_by_pk(
    id: 32
  ) {
    name
    email
    created_at
  }
}
```

#### Simple Object

[Table of contents](#table-of-contents)

Example:

```graphql
query {
  user {
    id
    name
    tags {
      id
      title
    }
    categories_aggregate {
      aggregate {
        count
      }
      nodes {
        title
      }
    }
  }
}
```

#### Aggregate object

[Table of contents](#table-of-contents)

```graphql
user_aggregate {
  aggregate {
    count
    sum {
      ...aggregateOnThisFields
    }
    avg {
      ...aggregateOnThisFields
    }
    max {
      ...aggregateOnThisFields
    }
    min {
      ...aggregateOnThisFields
    }
  }
  nodes {
    id
    name
    tags {
      title
    }
    categories_aggregate {
      aggregate {
        count
      }
      nodes {
        title
      }
    }
  }
}

fragment aggregateOnThisFields on UserResultAggregateType {
  id
}
```

Available aggregate functions: `min`, `max`, `sum`, `avg` and, `count`.

#### Arguments

[Table of contents](#table-of-contents)

##### Distinct

[Table of contents](#table-of-contents)

Example:

```graphql
query {
  user(distinct: true) {
    id
    name
    email
    created_at
  }
}
```

> :warning: For PostgreSQL users: There is no `distinct_on` support at the moment.

##### Where

[Table of contents](#table-of-contents)

Example:

```graphql
query {
  user(where: {email: {_like: "%example.com"}}) {
    name
    tags {
      title
    }
  }
}
```

###### _and

[Table of contents](#table-of-contents)

Queries are executed with `and` operator by default.

Example:

```graphql
query {
  user(
    where: {
      _and: {
        name: { _nlike: "%pattern%" },
        updated_at: { _year: "2020" }
      }
    }
  ) {
    id
    name
    email
    updated_at
  }
}
```

###### _or

[Table of contents](#table-of-contents)

Example:

```graphql
query {
  user(
    where: {
      _or: {
        name: { _nlike: "%pattern%" },
        updated_at: { _year: "2021" }
      }
    }
  ) {
    id
    name
    email
    updated_at
  }
}
```

###### _not

[Table of contents](#table-of-contents)

Example:

```graphql
query {
  user(
    where: {
      _not: {
        name: { _nlike: "%pattern%" },
        updated_at: { _year: "2020" }
      }
    }
  ) {
    id
    name
    email
    updated_at
  }
}
```

###### Operators

[Table of contents](#table-of-contents)

| Operator    | Equivalent       |
| :---------- | :--------------- |
| `_eq`       | `=`              |
| `_neq`      | `<>`,`!=`        |
| `_gt`       | `>`              |
| `_lt`       | `<`              |
| `_gte`      | `>=`             |
| `_lte`      | `<=`             |
| `_in`       | `IN`             |
| `_nin`      | `NOT IN`         |
| `_like`     | `LIKE`           |
| `_nlike`    | `NOT LIKE`       |
| `_ilike`    | `ILIKE`          |
| `nilike`    | `NOT ILIKE`      |
| `_similar`  | `SIMILAR TO`     |
| `_nsimilar` | `NOT SIMILAR TO` |
| `_is_null`  | `IS NULL`        |

> :warning: JSON operators are not yet supported.

##### Order

Example:

```graphql
query {
  user(
    order_by: { id: asc }
  ) {
    id
    name
  }
}
```

Available orders: `asc`, `desc`

[Table of contents](#table-of-contents)

##### Pagination

[Table of contents](#table-of-contents)

Example #1:

```graphql
query {
  user(
    limit: 5
    offset: 10
  ) {
    id
    name
  }
}
```

Example #2:

```graphql
query {
  user(
    take: 5
    skip: 10
  ) {
    id
    name
  }
}
```

### Mutation

[Table of contents](#table-of-contents)

#### insert

[Table of contents](#table-of-contents)

```graphql
mutation insert_example {
  insert_tag(
    objects: [
      {
        title: "Tag #1",
        user_id: 1
      },
      {
        title: "Tag #2",
        user_id: 1
      }
    ]
  ) {
    id
    title
  }
}
```

#### insert_one

[Table of contents](#table-of-contents)

```graphql
mutation insert_example {
  insert_tag_one(
    object: {
      name: "Tag #3",
      color: "Purple",
      user_id: 56
    }
  ) {
    id
    name
    created_at
  }
}
```

#### update_by_pk

[Table of contents](#table-of-contents)

```graphql
mutation update_example {
  update_tag_by_pk(
    pk_columns: {
      id: 5
    },
    _set: {
      name: "Tag #3 [updated]"
    }
  ) {
    name
    updated_at
  }
}
```

#### update

[Table of contents](#table-of-contents)

```graphql
mutation update_example {
  update_tag(
    where: {
      id: { _in: [3, 4, 5] }
    },
    _set: {
      color: "Cyan"
    }
  ) {
    id
    name
    color
    updated_at
  }
}
```

#### delete_by_pk

[Table of contents](#table-of-contents)

Example:

```graphql
mutation delete_example {
  delete_tag_by_pk(
    id: 5
  ) {
    id
  }
}
```

#### delete

[Table of contents](#table-of-contents)

Example:

```graphql
mutation delete_example {
  delete_tag(
    where: { color: { _is_null: true } }
  ){
    id
  }
}
```

### Subscription

[Table of contents](#table-of-contents)

Not supported.

## Authorization

[Table of contents](#table-of-contents)

### Override authorize

For defining authorization for each exposed GraphQL queries and mutation add a method of `set[NameOfOperation]Authorize` to its repective Pepper class:

```php
public function setQueryAuthorize(...$params)
{
    return ! Auth::guest();
}
```

The `...params` consist of `$root`, `array $args`, `$ctx`, `ResolveInfo $resolveInfo = null` and, `Closure $getSelectFields = null`. [Read more about params](https://github.com/rebing/graphql-laravel#authorization)

Available operations are:

- UpdateMutation
- InsertMutation
- DeleteMutation
- UpdateByPkMutation
- DeleteByPkMutation
- InsertOneMutation
- ByPkQuery
- AggregateQuery
- Query

### override authorization message

For defining authorization message for each exposed GraphQL queries and mutation add a method of `set[NameOfOperation]AuthorizationMessage` to its repective Pepper class, the return must should be string:

```php
public function setQueryAuthorizationMessage()
{
    return '(403) Not Authorized';
}
```

Available operations are:

- UpdateMutation
- InsertMutation
- DeleteMutation
- UpdateByPkMutation
- DeleteByPkMutation
- InsertOneMutation
- ByPkQuery
- AggregateQuery
- Query

## Authentication

[Table of contents](#table-of-contents)

Authentication is done using JWT utilizing [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth) package.

1. update `.env` file to include `JWT_SECRET` secret ([learn more](https://jwt-auth.readthedocs.io/en/develop/laravel-installation/)):

```bash
php artisan jwt:secret
```

2. Update you `User` model:

```php
<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

3. Configure Auth guard

in `config/auth.php` make sure to set:

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

...

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```


## Privacy

[Table of contents](#table-of-contents)

You can define privacy for individual fields as follow, `set[FieldName]Privacy` in Pepper class for each model:

```php
public function setEmailPrivacy($args)
{
    return false;
}
```

## Customization

[Table of contents](#table-of-contents)

### Override `count` method

In your pepper class, add the following method. the return type must be integer.

```php
public function resolveCountAggregate($root, $args, $context, $resolveInfo): int
{
    // override calculation of the count
}
```

### Override `avg` method

In your pepper class, add the following method. The return type must be an array.

```php
public function resolveAvgAggregate($root, $args, $context, $resolveInfo): array
{
    // override calculation of the avg
}
```

### Override `sum` method

In your pepper class, add the following method. The return type must be an array.

```php
public function resolveSumAggregate($root, $args, $context, $resolveInfo): array
{
    // override calculation of the sum
}
```

### Override `max` method

In your pepper class, add the following method. The return type must be an array.

```php
public function resolveMaxAggregate($root, $args, $context, $resolveInfo): array
{
    // override calculation of the max
}
```

### Override `min` method

In your pepper class, add the following method. The return type must be an array.

```php
public function resolveMinAggregate($root, $args, $context, $resolveInfo): array
{
    // override calculation of the min
}
```

### Override `description`

Create a new method called `set[operation]Description` and return a string to override description. available `operations` are:

- ResultAggregateType
- FieldAggregateUnresolvableType
- FieldAggregateType
- AggregateType
- Type
- UpdateMutation
- InsertMutation
- DeleteMutation
- UpdateByPkMutation
- DeleteByPkMutation
- InsertOneMutation
- ByPkQuery
- AggregateQuery
- Query
- MutationInput
- OrderInput
- Input

```php
public function setQueryDescription()
{
    return 'new desription';
}
```

## Authentication

[Table of contents](#table-of-contents)

1. set `pepper.auth.disabled` to `false` in order to enable authentication.
2. There should be a pepper class correspond to your defined user model. for
example if you have defined `App\Models\User::class` as your user model, you
must have `App\Pepper\User::class` class.

### Login

```graphql
{
  login(
    email: "amirmasoud@pepper.test"
    password: "12345678"
  ) {
    token
  }
}
```

return response would be JWT token if login credentials are valid, otherwise it would be authorization error.

#### Override login args

Add new method called `setLoginArgs` to the defined `User::class` class:

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;
use GraphQL\Type\Definition\Type;

class User extends GraphQL
{
    public function setLoginArgs(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
            'other_field' => ['other_field' => 'name', 'type' => Type::string()],
        ];
    }
}

```

### Register

```graphql
mutation {
  register(
    name: "amirmasoud"
    email: "amirmasoud@pepper.test"
    password: "12345678"
    password_confirmation: "12345678"
  ) {
    token
  }
}
```

Return response would be JWT token if no authorization error had been raised.

## Optimization

[Table of contents](#table-of-contents)

By enabling caching you can improve execution time:

1. set `pepper.cache.disabled` to `false`
2. set `graphql.lazyload_types` to `true`


## Roadmap

[Table of contents](#table-of-contents)

* ~~JWT Authentication~~
* Reset password
* ~~Validations~~
* ~~Authorization~~
* File upload
* JSON type support
* GIS support
* MySQL/PostgreSQL/SQLServer/SQLite custom fields support
* Subscription support
* Automatic result cache
* ReactAdmin support
* Route hashing

## License

The MIT License (MIT). Please see [License File](LICENSE) File for more information.
