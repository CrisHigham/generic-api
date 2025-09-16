# Generic Api

This Tina4 module is designed to provide a simple api with limited coding, taking advantage of the Tina4 ORM. The ORM
class names are used as the api endpoint names. The Api comes with built in security and env configuration.

## Installation

`composer require justblink/generic-api` This will also install Tina4 as a dependency.

Initialize Tina 4

`composer exec tina4 initialize:run`

## Setup

Please add the following to the .env file and set as desired

*Adds to the url between domain name and generic-api routes*

`GENERIC_API_BASE_URL=`

## Security

All endpoints are protected with the Tina4 `@secure` route protection. However please ensure that you have added security
to your satisfaction.

## Simple endpoints

All ORM objects should have a primary key `id`.

All endpoints can be extended with a prefix set in the .env file by setting the flag GENERIC_API_BASE_URL

Ping is an api verificaion endpoint `/ping`. This can be used to test security and that the api is working.

Get all rows - GET `/[class name]`

Get one row - GET `/[class name]/{id}`

Insert a row - POST `/[class name]`

Partially update a single row - PATCH `/[class name]/{id}`

Partially update a batch of rows - PATCH `/[class name]`

Completely update a single row - PUT `/[class name]/{id}`

Completely update a batch of rows - PUT `/[class name]`

Delete a row - DELETE `/[class name/{id}`

## Class names

Class names should be supplied lower case. Complex class names should be separated by "-".

For example:
`/my-class-name` will resolve to `MyClassName` in the ORM.

## Receiving data

All data is sent as JSON objects or array of objects to the endpoints.

PUT, PATCH and DELETE batch endpoints can receive data as an array or single record.

Care should be taken with the PUT endpoints, failure to specify all data could result in empty fields, especially if
field defaults are not set.

## Sending data

Endpoints will send error messages if things did not go well. All successful calls will result in sending all the data of 
the modified objects. 

Sending the ```nodata=1``` query parameter with each call, will result in just the affected id's being returned.

## Filters

These filters are available only on the get all rows endpoint. Filters can be added as query parameters in the form

```?field=operator:term```

#### Available operators ####
~~~
eq - field that is equal to the term 
ne - field that is not equal to the term
~~~

Concatenation of filters is acceptable

```?field1=operator:term&field2=operator:term```

## Validation

Adding a GenericApiValidation attribute to the ORM will invoke the built in validation. For example this will check if 
firstName is a string.

```
#[GenericApiValidation('string')]
public firstName;
```

By extending the ValidationHelper one can write custom validations or manipulate values before or after the normal validations, 
by using the beforeValidate() and afterValidate() functions. Each one has the ability to return if the validation was correct, 
the value as is (including manipulations), and an error message if required.

## Testing

There is a simple test application built around generic-api and can be found at CrisHigham/generic-api-example. This was built as the base for, and includes, a bruno testing suite and can be used as a base project at your own risk.

## Roadmap

* Ability to do validation
* Ability to add defaults
* Standardize the error pattern