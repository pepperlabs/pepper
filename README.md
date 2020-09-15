![Test](https://github.com/pepperlabs/pepper/workflows/Test/badge.svg?branch=master)

# Pepper

---

[WIP] Pepper is subject to change anything wihtout any prior notice at this stage of development.

## Version support

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

## Table of contents

- [Pepper](#pepper)
  - [Version support](#version-support)
  - [Installation](#installation)
  - [Table of contents](#table-of-contents)
  - [Introducation](#introducation)
    - [Installation](#installation-1)
    - [Supported databases](#supported-databases)
  - [Background](#background)
  - [Commands](#commands)
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
  - [Acknowledgement](#acknowledgement)
  - [Contribution](#contribution)
    - [Report bugs](#report-bugs)
    - [Feature request](#feature-request)
    - [Pull request](#pull-request)
  - [Support](#support)
  - [License](#license)

## Introducation

[Table of contents](#table-of-contents)

Pepper is an auto generative GraphQL based on [Laravel wrapper for Facebook's GraphQL](https://github.com/rebing/graphql-laravel).
The goal is simplify and fasten development of GraphQL based APIs.

### Installation

### Supported databases

## Background

[Table of contents](#table-of-contents)

The idea of the extension came from other open source project called [Hasura](https://hasura.io/). Hasura only supports PostgreSQL and in this projects, Pepper
we are supporting MySQL, PostgreSQL, SQLite and SQL Server. Although most of the queries and mutation structure are similar there is no guarantee to be equal.

## Commands

[Table of contents](#table-of-contents)

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

## Roadmap

* JWT Authentication
* Validations
* Authorization
* File upload
* JSON type support
* GIS support
* MySQL/PostgreSQL/SQLServer/SQLite custom fields support
* Subscription support
* Automatic result cache
* ReactAdmin support

## Acknowledgement

[Table of contents](#table-of-contents)

## Contribution

[Table of contents](#table-of-contents)

### Report bugs

[Table of contents](#table-of-contents)

### Feature request

[Table of contents](#table-of-contents)

### Pull request

[Table of contents](#table-of-contents)

## Support

[Table of contents](#table-of-contents)

## License

The MIT License (MIT). Please see [License File](LICENSE) File for more information.
