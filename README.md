[![Latest Stable Version](https://poser.pugx.org/dmamontov/gollos-restapi/v/stable.svg)](https://packagist.org/packages/dmamontov/agollos-restapi)
[![License](https://poser.pugx.org/dmamontov/gollos-restapi/license.svg)](https://packagist.org/packages/dmamontov/gollos-restapi)
[![Total Downloads](https://poser.pugx.org/dmamontov/gollos-restapi/downloads.svg)](https://packagist.org/packages/dmamontov/gollos-restapi)

Gollos Rest API Client
======================

This class can eanage e-commerce operations using the [Gollos API](https://gollos.com/).

It can send HTTP requests to the Gollos API Web server to perform several types of operations with customers, vendors, products, orders, etc..

Currently it can retrieve, add, update and delete products, groups, customers, vendors, products and orders.

## Requirements
* PHP version >5.0
* curl

## Available methods
* `getProducts`, `addProducts`, `updateProducts`, `removeProducts`
* `getGroups`, `addGroups`, `updateGroups`, `removeGroups`
* `getVendors`, `addVendors`, `updateVendors`, `removeVendors`
* `getCustomers`, `addCustomers`, `updateCustomers`, `removeCustomers`
* `getOrders`, `removeOrders`

## Installation

1) Install [composer](https://getcomposer.org/download/)

2) Follow in the project folder:
```bash
composer require dmamontov/gollos-restapi ~1.0.0
```

In config `composer.json` your project will be added to the library `dmamontov/gollos-restapi`, who settled in the folder `vendor/`. In the absence of a config file or folder with vendors they will be created.

If before your project is not used `composer`, connect the startup file vendors. To do this, enter the code in the project:
```php
require 'path/to/vendor/autoload.php';
```

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
