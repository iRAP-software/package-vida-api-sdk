ViDA SDK
=========
This is a package to help developers write code that interfaces with ViDA.

**The ViDA SDK is still under development**, but once complete, it will provide a simple interface for developers who wish to use the ViDA API.

## Getting started

Here are some starting points for using the SDK

### Connecting to the API
To connect, you create a new client object and pass it your developer credentials...

```php
$api = new Client(
  $auth_id,
  $api_key,
  $private_key
);
```
...then you pass in the user access token you've been given:
```php
$api->setUserToken(
  $user_auth_id,
  $user_api_key,
  $user_private_key
);
```
And that's it. All your authentication needs are dealt with automatically by the SDK from hereon in.

### Accessing and adding Resources
Accessing a resource can be as easy as:
```php
$users = $api->getUsers();
```

...while adding a resource can be done like this:

```php
$response = $api->addUser(
  $name,
  $email,
  $password
);
```
