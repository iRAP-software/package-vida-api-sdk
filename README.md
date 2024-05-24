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

To connect via User(), create a new User() object and pass it the App credentials supplied to you by iRAP AND the User credentials fetched with `requestUserPermissions()` (or supplied to you by iRAP):

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
To make a request on a user's behalf requires a user token which can be fetched with the following request:

```php
$api->requestUserPermissions($returnUrl);
```
This will redirect the user to the SSO service, where after successful login and granting permissions will redirect back you to the specified `$returnUrl` url with the following response on success:

```php
$response->userAuthId;
$response->userApiKey;
$response->userPrivateKey;
```
These should be saved in your app for use in all future API calls, and can be used to instantiate a User object as follows:

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

### Accessing and adding Resources
Accessing a resource can be as easy as:
```php
$datasets = $api->getDatasets();
```

...while adding a resource can be done like this:

```php
$response = $api->addDataset($name, 
  $project_id, 
  $manager_id,
  $country_id,
  $type,
  $assessment_date,
  $description);
```

If you wish to access a specific resource, you should pass the
resource ID to the method being called, e.g.

```php
$dataset_details = $api->getDatasets(1);
```

...will populate $dataset_details with the dataset details for the dataset with id 1. Using the Get methods without specifying a resource ID will return a list of the resources you have access to.

### Updating Resources
To update a resource, you must use a replace method. The replace methods take the resource ID for the resource you wish to update, as well as the values you wish to update the resource with.

The required fields vary from resource type to resource type, but for example, the request for updating a dataset would look like this:

```php
$response = $api->replaceDataset(
  $id, 
  $name, 
  $project_id, 
  $manager_id
);
```

**N.B. All the fields are mandatory, even if you only wish to change the value of one of them.**

### Deleting Resources
To delete a resource, you must use a delete method. The delete methods take the resource ID for the resource you wish to delete.

To delete a dataset, use the following method:

```php
$response = $api->deleteDataset($id);
```

This would delete the dataset with the ID specified in $id.

### Filters
By default, a Get method for a resource will return all of the results that are available for the authenticated user. This can be useful, but gives you extra work to do if you only require a subset of results.

To make this easier, the SDK includes a filter object that allows you to only return the criteria you are looking for. You can filter on any of the field names in the returned results and use any one of the following operations:

**field = value**

**field != value**

**field > value**

**field < value**

**field >= value**

**field <= value**

The simplest use of the filter would look like this:

```php
$filter = new iRAP\VidaSDK\Filter('id', 1);
```

To use the filter, you must then include it in your request:

```php
$result = $api->getUsers(null, $filter);
```

This request will return users where the id field equals 1.

**N.B. Equals (=) is the default operator for a filter, therefore there is no need to include it explicitly.**

If you wanted to see all the users whose id does not equal 1, you could do this:

```php
$filter = new iRAP\VidaSDK\Filter('id', 1, '!=');
$result = $api->getUsers(null, $filter);
```

Notice that the != operator is specified as the third parameter.

### Multiple Filters
Filtering on a single field is useful, but sometimes you will need to filter on more than one field. This can be done by calling the addFilter() method on the filter object. 
This is an "AND" relationship (all criteria must pass, rather than just one of them).

```php
$filter = new iRAP\VidaSDK\Filter('id', 1, '!=');
$filter->addFilter('is_admin', 1);
$result = $api->getUsers(null, $filter);
```

Now you are looking for a user whose id does not equal 1 AND who is an admin. The addFilter method will also take an operator parameter, so you can use different operators on different fields. As before, the default is equals (=). You can call the addFilter() method as many times as you wish, to create the filter criteria you require.

### Filter Groups
In some situations, you may wish to perform searches on alternative sets of criteria, without having to run two separate queries. For this, we have filter groups. These allow you to pass in multiple filter objects with alternative search options. For example:

```php
# Create the first filter for the first admins that were put into the system
$filter1 = new iRAP\VidaSDK\Filter('id', 10, '<=');
$filter1->addFilter('is_admin', 1);

# Create second filter for newer users that are not admins.
$filter2 = new iRAP\VidaSDK\Filter('id', 1000, '>');
$filter2->addFilter('is_admin', 1, '!=');

# Group the filters together
$filter = new iRAP\VidaSDK\FilterGroup(iRAP\VidaSDK\Conjunction::createOr(), $filter1, $filter2);

$result = $api->getUsers(null, $filter);
```

In this example, results will be returned if either the first group of filters are satisfied, OR the second group of filters are satisfied. Note that the two filter objects are passed into the FilterGroup object inside an array. You can include as many filter objects as you wish.

For really advanced requirements, you can nest filter groups inside each other, allowing highly specific filtering to be applied to results. This should cater for almost all use cases, but if not, filtering on the application layer will still be necessary.

### Resources Available
A list of the different resource types available, and the methods for accessing them, can be found in Controllers/ApiInterface.php in the SDK package. This file will continue to be added to as new methods become available in the API.

### Requirements
The only requirement for the SDK to run is PHP 5.6 or newer.
