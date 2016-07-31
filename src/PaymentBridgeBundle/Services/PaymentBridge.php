<?php
namespace PaymentBridgeBundle\Services;
use PaymentSuite\PaymentCoreBundle\Services\Interfaces\PaymentBridgeInterface;
class PaymentBridge implements PaymentBridgeInterface
{
/**
* @var Object
*
* Order object
*/
private $order;
/**
* Set order to PaymentBridge
*
* @var Object $order Order element
*/
public function setOrder($order)
{
$this->order = $order;
}
/**
* Return order
*
* @return Object order
*/
public function getOrder()
{
return $this->order;
}
/**
* Return order identifier value
*
* @return integer
*/
public function getOrderId()
{
return $this->order->getId();
}
/**
* Given an id, find Order
*
* @return Object order
*/
public function findOrder($orderId)
{
/*
* Your code to get Order
*/
return $this->order;
}
/**
* Get the currency in which the order is paid
*
* @return string
*/
public function getCurrency()
{
/*
* Set your static or dynamic currency
*/
return 'USD';
}
/**
* Get total order amount in cents
*
* @return integer
*/
public function getAmount()
{
/*
* Return payment amount (in cents)
*/
return 30;
}
/**
* Return if order has already been paid
*
* @return boolean
*/
public function isOrderPaid()
{
return array();
}
/**
* Get extra data
*
* @return array
*/
public function getExtraData()
{
return false;
}
}
