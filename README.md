# Pepper

## Table of contents

- [Pepper](#pepper)
  - [Table of contents](#table-of-contents)
  - [Introducation](#introducation)
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

## Introducation

[Table of contents](#table-of-contents)

## Background

[Table of contents](#table-of-contents)

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

#### Aggregate object

[Table of contents](#table-of-contents)

#### Arguments

[Table of contents](#table-of-contents)

##### Distinct

[Table of contents](#table-of-contents)

##### Where

[Table of contents](#table-of-contents)

##### Order

[Table of contents](#table-of-contents)

##### Pagination

[Table of contents](#table-of-contents)

### Mutation

[Table of contents](#table-of-contents)

#### insert

[Table of contents](#table-of-contents)

#### insert_one

[Table of contents](#table-of-contents)

#### update_by_pk

[Table of contents](#table-of-contents)

#### update

[Table of contents](#table-of-contents)

#### delete_by_pk

[Table of contents](#table-of-contents)

#### delete

[Table of contents](#table-of-contents)

### Subscription

[Table of contents](#table-of-contents)

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
