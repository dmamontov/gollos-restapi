<?
require 'GollosRestApi.php';

//Getting information about the leads
$gollos = new GollosRestApi($key, $secretKey);
$order = $gollos->getOrders(array('id' => 25648));

//Creating a client
$gollos = new GollosRestApi($key, $secretKey);
$customer = array(
    'first_name' => 'Test',
    'last_name'  => 'Test',
    'username' => 'dtest',
    'password' => uniqid(),
    'ip' => '85.198.127.82'
);
$result = $gollos->addCustomers($customer);
?>