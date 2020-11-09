![Test](https://github.com/pepperlabs/pepper/workflows/Test/badge.svg?branch=master)

# Pepper

Pepper is a Laravel package that can expose GraphQL endpoint for your defined models with minimun configuration.

## Table of contents

- [Pepper](#pepper)
  - [Table of contents](#table-of-contents)
  - [Introducation](#introducation)
    - [Version support](#version-support)
  - [Installation](#installation)
    - [Optimization](#optimization)
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
  - [Accessors & Mutators](#accessors--mutators)
    - [Defining An Accessor](#defining-an-accessor)
    - [Defining A Mutator](#defining-a-mutator)
  - [Upload file](#upload-file)
  - [Customization](#customization)
    - [Change field type](#change-field-type)
    - [Override `count` method](#override-count-method)
    - [Override `avg` method](#override-avg-method)
    - [Override `sum` method](#override-sum-method)
    - [Override `max` method](#override-max-method)
    - [Override `min` method](#override-min-method)
    - [Override `description`](#override-description)
    - [Customizing Authentication](#customizing-authentication)
      - [Login](#login)
        - [Override login args](#override-login-args)
        - [Set username for login](#set-username-for-login)
      - [Register](#register)
      - [Override register args](#override-register-args)
        - [Override resolve method](#override-resolve-method)
        - [Override authorize method](#override-authorize-method)
        - [Override authorization message method](#override-authorization-message-method)
        - [Override rules method](#override-rules-method)
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

Out of the box any models selected would be available at the graphql endpoint.
however you should make sure that you have defined the return type of your
relations in your models in order to make the relations work on the fly.
An example model would look like this:

```php
<?php

namespace Your\App\Models\Namaspace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
```

### Optimization

By enabling caching you can improve execution time:

1. set `pepper.cache.disabled` to `false`
2. set `graphql.lazyload_types` to `true`

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

| Operator    | Equivalent       | Note            |
| :---------- | :--------------- | :-------------- |
| `_eq`       | `=`              |                 |
| `_neq`      | `<>`,`!=`        |                 |
| `_gt`       | `>`              |                 |
| `_lt`       | `<`              |                 |
| `_gte`      | `>=`             |                 |
| `_lte`      | `<=`             |                 |
| `_in`       | `IN`             |                 |
| `_nin`      | `NOT IN`         |                 |
| `_like`     | `LIKE`           |                 |
| `_nlike`    | `NOT LIKE`       |                 |
| `_ilike`    | `ILIKE`          | PostgreSQL only |
| `nilike`    | `NOT ILIKE`      | PostgreSQL only |
| `_similar`  | `SIMILAR TO`     |                 |
| `_nsimilar` | `NOT SIMILAR TO` |                 |
| `_is_null`  | `IS NULL`        |                 |

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

4. Enable authentication query and mutation in `config/pepper.php`:

```php

...

'auth' => [
    'disabled' => false,

    ...
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

## Accessors & Mutators

[Table of contents](#table-of-contents)

### Defining An Accessor

### Defining A Mutator

## Upload file

[Table of contents](#table-of-contents)

1. Change field type to Upload type:

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL as ParentGraphQL;

class User extends GraphQL
{
    public function setExampleType()
    {
        return ParentGraphQL::type('Upload');
    }
}
```

## Customization

[Table of contents](#table-of-contents)

### Change field type

In the respective Pepper class add a method with following format: `set[FieldName]Type`

For example if we want to change `ID` column type on `User` model, we could add following method to our User Pepper class:

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;
use GraphQL\Type\Definition\Type;

class User extends GraphQL
{
    public function setIdType()
    {
        return Type::string();
    }
}
```

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

### Customizing Authentication

[Table of contents](#table-of-contents)

1. set `pepper.auth.disabled` to `false` in order to enable authentication.
2. There should be a pepper class correspond to your defined user model. for
example if you have defined `App\Models\User::class` as your user model, you
must have `App\Pepper\User::class` class.

#### Login

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

##### Override login args

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

##### Set username for login

The default args for login are `email` and `password`, however, you can change
username by defining a method called `setLoginUsernameField` in your pepper
class which corresponds to `User::class` class:

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;

class User extends GraphQL
{
    public function setLoginUsernameField(): string
    {
        return 'username';
    }
}
```

#### Register

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

#### Override register args

Add `setRegisterArgs` method in Pepper `User` class:

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;
use GraphQL\Type\Definition\Type;

class User extends GraphQL
{
    public function setRegisterArgs(): string
    {
        return [
            'name' => ['name' => 'name', 'type' => Type::string()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
            'password_confirmation' => ['name' => 'password_confirmation', 'type' => Type::string()],
        ];
    }
}
```

##### Override resolve method

Add `setRegisterResolve` method in Pepper `User` class. `$args` and `$user` arguments
are available. the return of this method should be user class instance.

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;
use Illuminate\Support\Facades\Hash;

class User extends GraphQL
{
    public function setRegisterResolve($args, $user)
    {
        return $user::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);
    }
}
```

##### Override authorize method

Add `setRegisterAuthorize` method in Pepper `User` class. the return of this
method must be boolean.

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;

class User extends GraphQL
{
    public static function setRegisterAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields)
    {
        return true;
    }
}
```

##### Override authorization message method

Add `setRegisterAuthorizationMessage` method in Pepper `User` class. the return
of this method must be string.

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;

class User extends GraphQL
{
    public static function setRegisterAuthorizationMessage()
    {
        return 'Validation error';
    }
}
```

##### Override rules method

Add `setRegisterRules` method in Pepper `User` class. the return of this method
must be array.

```php
<?php

namespace App\Http\Pepper;

use Pepper\GraphQL;

class User extends GraphQL
{
    public static function setRegisterRules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
```

## Roadmap

[Table of contents](#table-of-contents)

* ~~JWT Authentication~~
* Reset password
* 2 factor authentication
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
