<?php

require_once __DIR__ . '/src/BitsianClient.php';


$client = new BitsianClient();

// connected websocket

$client -> connect('kraken', 'btc', 'usd');

// connected Rest API

$client -> getCurrencies();

$client -> getProducts(5);

$client -> getExchanges();

$client -> getBalance(1,1);

$client -> getOrders('open');

$client -> getOrder('216c4346-533a-45f1-a5da-8ecd4f168512');

$createOrderDto = array("orderSide" => "buy",
    "currencyPair" => "LTC-USD",
    "quantity" => 0.1,
    "price" => 62.89,
    "orderType" => "market",
    "exchangeId" => 4);

$client -> createOrder($createOrderDto);

$client -> cancelOrder('216c4346-533a-45f1-a5da-8ecd4f168512');

