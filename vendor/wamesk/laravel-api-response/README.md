# Laravel Api Response

## Laravel package for easy formatted api response

**Installation**

```bash
composer require wamesk/laravel-api-response
```

**Usage**

For basic response use class and call response() function and pass status code needed *(default 200)*.

This will not send any data itself, this function is used last to generate response and set status code.

```php
return ApiResponse::response(201);
```

Response:
```json
{
    "data": null,
    "code": null,
    "errors": null,
    "message": null
}
```

You can also pass message in your response by adding `message()` function before response function.

```php
return ApiResponse::message('Hello')->response(201);
```

Response:

```json
{
  "data": null,
  "code": null,
  "errors": null,
  "message": "Hello"
}
```

You can pass internal code using `code()` function that helps you find of response in case of error.

```php
return ApiResponse::code('1.2')->message('Hello')->response(201);
```

Response:

```json
{
  "data": null,
  "code": "1.2",
  "errors": null,
  "message": "Hello"
}
```

If you don't use `message()` function but use `code()` function, and it will try to translate your code to message.

You can also set prefix of translation as second parameter *(Default is 'api')*.

```php
return ApiResponse::code('1.2', 'user')->response(201); // return "message": "user.1.2" as in Response example

return ApiResponse::code('1.2')->response(201); // When not provided second parameter it will use default and return "message": "api.1.2"
```

Response:

```json
{
  "data": null,
  "code": "1.2",
  "errors": null,
  "message": "user.1.2"
}
```

When not provided second parameter

```json
{
  "data": null,
  "code": "1.2",
  "errors": null,
  "message": "api.1.2"
}
```

You can pass data using `data()` function.

```php
return ApiResponse::data(['id' => 1, 'name' => 'Jhon Jhonson'])->code('1.2')->message('Hello')->response(201);
```

Response:

```json
{
  "data": {
    "id": 1,
    "name": "Jhon Jhonson"
  },
  "code": "1.2",
  "errors": null,
  "message": "Hello"
}
```

If you want to inform frontend about some error you can use `errors()` function.

```php
return ApiResponseDev::errors(['email' => 'Email is required'])->response(201);
```

Response:

```json
{
  "data": null,
  "code": null,
  "errors": {
    "email": "Email is required"
  },
  "message": null
}
```

In case you need pagination in your api you can use `collection()` function instead of `data()` function.
You can use this function by passing paginated data, and you can also pass Resource for better data formatting *(Resource is not required)*

```php
$users = User::paginate(10);

return ApiResponse::collection($users, UserResource::class)->code('1.2')->message('Hello')->response(201);
```

Response:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Jhon Jhonson",
        },
        {
            "id": 2,
            "name": "Patrick Jhonson",
        }
    ],
    "links": {
        "first": "http://localhost:8888/api/v1/test?page=1",
        "last": "http://localhost:8888/api/v1/test?page=2",
        "prev": null,
        "next": "http://localhost:8888/api/v1/test?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            {
                "url": null,
                "label": "pagination.previous",
                "active": false
            },
            {
                "url": "http://localhost:8888/api/v1/test?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "http://localhost:8888/api/v1/test?page=2",
                "label": "2",
                "active": false
            },
            {
                "url": "http://localhost:8888/api/v1/test?page=3",
                "label": "3",
                "active": false
            },
            {
                "url": "http://localhost:8888/api/v1/test?page=2",
                "label": "pagination.next",
                "active": false
            }
        ],
        "path": "http://localhost:8888/api/v1/test",
        "per_page": 2,
        "to": 2,
        "total": 6
    },
    "code": "1.2",
    "errors": null,
    "message": "Hello"
}
```
