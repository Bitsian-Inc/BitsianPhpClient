<?php

require_once('vendor/autoload.php');
require_once("Constants.php");
require_once("authentication.php");
require_once("private_calls.php");

class BitsianClient
{
    /**
     * Get exchange list.
     */
    function getExchanges()
    {

        $response = callAPI(Constants::GET,  Constants::EXCHANGE_ENDPOINT, null);

        echo $response;

    }

    /**
     * Get currency list.
     */
    function getCurrencies() {

        $response = callAPI(Constants::GET,  Constants::CURRENCY_ENDPOINT, null);

        echo $response;
    }

    /**
     * Get product list for given exchange.
     *
     * @param Integer $exchangeId exchange id
     */
    public function getProducts($exchangeId) {

        $response = callAPI(Constants::GET,  Constants::PRODUCT_ENDPOINT . '?exchangeId=' .$exchangeId , null);

        echo $response;
    }


    /**
     * Get account balance details.
     * @param String $exchangeId
     * @param String $currencyId
     */
    function getBalance($exchangeId, $currencyId) {

        $response = callAPI(Constants::GET,  Constants::BALANCE_ENDPOINT . '?exchangeId=' . $exchangeId . '&currencyId=' . $currencyId, null);

        echo $response;
    }

    /**
     * Get all orders details.
     *
     * @param String $resolution open or close
     */
    function getOrders($resolution) {

        $response = callAPI(Constants::GET,  Constants::ORDER_DETAILS . '?resolution='. $resolution , null);

        echo $response;
    }

    /**
     * Get order details for given order id.
     *
     * @param String $orderId bitsian order id
     */
    function getOrder($orderId) {

        $response = callAPI(Constants::GET,  Constants::ORDER_DETAILS . '/' . $orderId , null);

        echo $response;
    }


    /**
     * Create new order.
     *
     * @param array $createOrderDto create order dto
     */
    function createOrder($createOrderDto) {

        $response = callAPI(Constants::POST,  Constants::ORDER_DETAILS , json_encode($createOrderDto));

        echo $response;

    }


    /**
     * Cancel the order for given id.
     *
     * @param String $orderId bitsian order id
     */
    function cancelOrder($orderId) {

        $response = callAPI(Constants::POST,  Constants::ORDER_DETAILS . '/' . $orderId . Constants::ORDER_CANCEL , null);

        echo $response;
    }


    /**
     * Connect to bitsian websocket to get live order book and trades.
     *
     * @param String $exchange      exchange name
     * @param String $baseCurrency  base bitsian currency
     * @param String $quoteCurrency quote bitsian currency
     */
    function connect($exchange, $baseCurrency, $quoteCurrency)
    {

        \Ratchet\Client\connect(Constants::WEBSOCKET_END_POINT)->then(function ($conn) use ($exchange , $baseCurrency, $quoteCurrency) {

            $nonce = round(microtime(true) * 1000);

            $signature = generateSignature($nonce, Constants::GET, Constants::WEBSOCKET_PATH, "");

            echo $signature . "\n";

            $authentication_subscription = format_auth_subscription($nonce, $signature);

            $conn->send($authentication_subscription);


            $conn->on('message', function (\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn , $exchange, $baseCurrency, $quoteCurrency) {
                echo "Received: " . explode("\n\n",$msg -> getPayload())[1]  ."\n";

                if (strpos($msg, 'Error') === false) {

                    $exchangeOrderBookChannel = "/v1/productbest/" . $exchange . '/' . $baseCurrency . '/' . $quoteCurrency;

                    $exchangeTradeTapeChannel = '/v1/tradetape/' . $exchange . '/' . $baseCurrency . '/' . $quoteCurrency;

                    $orderBookChannel = '/v1/productbest/'. $baseCurrency . '/' . $quoteCurrency;

                    $tradeTapeChannel = '/v1/tradetape/' . $baseCurrency . '/' . $quoteCurrency;

                    $subscription = format_subscription($exchangeOrderBookChannel);
                    $conn->send($subscription);

                    $subscription = format_subscription($exchangeTradeTapeChannel);
                    $conn->send($subscription);

                    $subscription = format_subscription($orderBookChannel);
                    $conn->send($subscription);

                    $subscription = format_subscription($tradeTapeChannel);
                    $conn->send($subscription);
                }
            });

            $conn->on('close', function($code = null, $reason = null) {
                echo "Connection closed ({$code} - {$reason})\n";
            });


        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });


        /**
         * Authentication subscription format should like this  "SUBSCRIBE\ndestination:/v1/auth\nid:1\n\n\x00\n"
         *
         * @param $nonce
         * @param $signature
         * @return string return auth subscription
         */
        function format_auth_subscription($nonce, $signature){

            $str = Constants::SUBSCRIBE . "\n" . Constants::DESTINATION . ":". Constants::AUTHENTICATION_CHANNEL . "\n" . Constants::ID  . ":" . "1\n"
                ."BITSIAN-API-KEY:" . getenv(Constants::API_KEY ) .  "\nBITSIAN-TIMESTAMP:" . $nonce . "\nBITSIAN-API-SIGN:" . $signature ."\nBITSIAN-PASSPHRASE:"
                . getenv(Constants::PASS_PHRASE ) ."\n\n\x00\n";

            return $str;

        }

        /**
         *  order book and trade tape subscription format
         * @param $destination
         * @return string  formatted subscription
         */
        function format_subscription($destination){

            $str = Constants::SUBSCRIBE . "\n" . Constants::DESTINATION . ":". $destination . "\n" . Constants::ID  . ":" . "2\n" ."\n\x00\n";

            return $str;

        }

    }


}



