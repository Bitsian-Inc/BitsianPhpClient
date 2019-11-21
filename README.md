# bitsian - php client

 > A full-featured Bitsian API client for php

- [x] Supports all documented v1.1 endpoints
## Getting started

Clients for both the [REST API](https://docs.bitsian.com/#rest) and
[streaming WebSocket API](https://docs.bitsian.com/#websocket) are included.
Private endpoints as indicated in the API docs require authentication with an API
key and secret key.

Add API key, secret key and passPhrase in the environment variables.

```php
$client = new BitsianClient();
```

You can learn about the API responses of each endpoint [by reading our
documentation](http://docs.bitsian.com/).

## Methods for Exchange Data
For Exchange data methods, No permission needed for API key.

* Get all the exchanges in bitsian
```php
$client -> getExchanges();
```
* Get all the bitsian supported currencies
```php
$client -> getCurrencies();
```
* Get the exchange products list by exchange
```php
$client -> getProducts(2);
```

## Methods for Account
API key need 'balance' permission.
* Get the balance info (filtered by exchange as well as currency)
```php
$client -> getBalance(2,1);
```

## Methods for Order
API key need 'trade' permission for order methods.
* Get all the orders (open / completed)
```php
$client -> getAllOrders('open');
```
* Get an order info
```php
$client -> getOrder('c5a28f8f-6860-4b12-a005-930f3781e195');
```
* Create an order
```php
$order = array("orderSide" => "buy",
          "currencyPair" => "LTC-USD",
          "quantity" => 0.1,
          "price" => 62.89,
          "orderType" => "market",
          "exchangeId" => 4);

$client -> createOrder(order);
```
* Cancel a particular order
```php
$order -> cancelOrder('c5a28f8f-6860-4b12-a005-930f3781e195');
```

## Websocket Client

Once authenticated successful with API keys, subscription of pairs can initiated for getting real time updates.

```php

$client -> connect('kraken', 'btc', 'usd');

```
