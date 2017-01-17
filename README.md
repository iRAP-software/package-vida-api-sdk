ViDA SDK
=========
This is a package to help developers write code that interfaces with ViDA.

The ViDA SDK is still under development, but once complete, it will provide a simple interface for developers who wish to use the ViDA API.

## Getting started

Here are some starting points for using the SDK

### Connecting to the API
There are two classes for connecting to the API. The first, App(), can be used to make requests that don't require user authentication, such as requesting a user token. The second, User() is used for almost everything else.

To connect via App(), create a new App() object and pass it the App credentials supplied to you by iRAP:

```php
$api = new App(
  $app_auth_id,
  $app_api_key,
  $app_private_key
);
```
You can then request a user token. See [Getting a user token](#getting-a-user-token)

To connect via User(), create a new User() object and pass it the App credentials supplied to you by iRAP AND the User credentials fetched with getUserToken() (or supplied to you by iRAP):

```php
$api = new User(
  $app_auth_id,
  $app_api_key,
  $app_private_key,
  $user_auth_id,
  $user_api_key,
  $user_private_key
);
```

And that's it. All your authentication needs are dealt with automatically by the SDK from hereon in.

### Responses
Requests to the ViDA API will return a response object in the SDK. The content of the response object may vary, depending on the request, but typically you will find the following properties within the object:

```php
/*  The response body i.e. the results
 *  that are returned
 */
$response->response;

/*  The HTTP Response code,
 *  e.g. 200 for success,
 *  401 for unauthorized
 */
$response->code;

/*  Status of the response:
 *  Success or Error
 */
$response->status;

/*  The error message, if status == 'Error'
 */
$response->error;
```
It is important to check these properties in order to make sense of the response, for example an empty response property may signal an error, but it may indicate that there were no applicable results for your request.

### Permissions
In order to access a particular resource or method, your app must have permission to use it. Permissions are set up by iRAP, based on what we think your app needs to be able to do.

If you try to access a resource without the correct permissions, you will get the following response:

```php
$response->status = 'Error';
$response->code = 401;
$response->error = 'Authentication failure - You do not have permission to access this resource';
```
If you feel that you need access to a resource or method that you do not have permission to access, you can email iRAP on support@irap.org to request permission. Please provide an explanation of what you are trying to achieve.

### Getting a user token
If your app has permission to request a user token by supplying the user's email address and password, this can be done with the following request:

```php
$api->getUserToken($email, $password);
```
This will return the following response on success:

```php
$response->userAuthId;
$response->userApiKey;
$response->userPrivateKey;
```
These should be saved in your app for use in all future API calls, and can be used to instatiate a User object as follows:

```php
$api = new User(
  $app_auth_id,
  $app_api_key,
  $app_private_key,
  $user_auth_id,
  $user_api_key,
  $user_private_key
);
```
**Please Note: It is strictly against the API Terms of Use to store the user's password locally in your application. Once the user token has been received, you will have no further need for the password.**

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

If you wish to access a specific resource, you should pass the
resource ID to the method being called, e.g.

```php
$user_details = $api->getUsers(1);
```

...will populate $user_details with the account details for the user who's user ID is 1. Using the Get methods without specifying a resource ID will return a list of the resources you have access to.

### Updating Resources
To update a resource, you must use a replace method. The replace methods take the resource ID for the resource you wish to update, as well as the values you wish to update the resource with.

The required fields vary from resource type to resource type, but for example, the request for updating the user would look like this:

```php
$response = $api->replaceUser(
  $id,
  $name,
  $email,
  $password
);
```

**N.B. All the fields are mandatory, even if you only wish to change the value of one of them.**

### Deleting Resources
To delete a resource, you must use a delete method. The delete methods take the resource ID for the resource you wish to delete.

To delete a user, use the following method:

```php
$response = $api->deleteUser($id);
```

This would delete the user with the ID specified in $id.

### Resources Available
A list of the different resource types available, and the methods for accessing them, can be found in apiInterface.php in the root directory of the SDK package. This file will continue to be added to as new methods become available in the API.

### Requirements
The only requirement for the SDK to run is PHP 5.6 or newer.
