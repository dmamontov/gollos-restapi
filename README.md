PHPClient to work with through Gollos Rest API
==============================================

PHPClient to work with through  [Gollos Rest API](https://gollos.com/).

## Requirements
* PHP version >5.0

## Available methods
* `getProducts`, `addProducts`, `updateProducts`, `removeProducts`
* `getGroups`, `addGroups`, `updateGroups`, `removeGroups`
* `getVendors`, `addVendors`, `updateVendors`, `removeVendors`
* `getCustomers`, `addCustomers`, `updateCustomers`, `removeCustomers`
* `getOrders`, `removeOrders`

## Examples of use

### Getting information about the order

``` php
$gollos = new GollosRestApi($key, $secretKey);
$order = $gollos->getOrders(array('id' => 25648));
```
### Creating a client

``` php
$gollos = new GollosRestApi($key, $secretKey);
$customer = array(
    'first_name' => 'Test',
    'last_name'  => 'Test',
    'username' => 'dtest',
    'password' => uniqid(),
    'ip' => '85.198.127.82'
);
$result = $gollos->addCustomers($customer);
```
