php-sdk
=======

Provides tools for building modules that integrate Tiresias into your e-commerce platform.

## Requirements

* PHP 5.2+

## What's included?

### Classes

* **TiresiasApiRequest** class for making API requests to the Tiresias APIs
* **TiresiasApiToken** class that represents an API token which can be used whn making authenticated requests to the Tiresias APIs
* **TiresiasCollection** collection base class
* **TiresiasProductCollection** collection class for tiresias product objects
* **TiresiasOrderCollection** collection class for tiresias order objects
* **TiresiasException** main exception class for all exceptions thrown by the SDK
* **TiresiasHttpException** HTTP request exceptions upon request failure
* **TiresiasExporter** class for exporting encrypted historical data from the shop
* **TiresiasExportOrderCollection** class for exporting historical order data
* **TiresiasExportProductCollection** class for exporting historical product data
* **TiresiasHelper** base class for all tiresias helpers
* **TiresiasHelperDate** helper class for date related operations
* **TiresiasHelperIframe** helper class for iframe related operations
* **TiresiasHelperPrice** helper class for price related operations
* **TiresiasHttpRequest** class for making HTTP request, supports both curl and socket connections
* **TiresiasHttpRequestAdapter** base class for creating http request adapters
* **TiresiasHttpRequestAdapterCurl** http request adapter for making http requests using curl
* **TiresiasHttpRequestAdapterSocket** http request adapter for making http requests using sockets
* **TiresiasHttpResponse** class that represents a response for an http request made through the TiresiasHttpRequest class
* **TiresiasOAuthClient** class for authorizing the module to act on the Tiresias account owners behalf using OAuth2 Authorization Code method
* **TiresiasOAuthToken** class that represents a token granted using the OAuth client
* **TiresiasOperationProduct** class for performing create/update/delete operations on product object
* **Tiresias** main sdk class for common functionality
* **TiresiasAccount** class that represents a Tiresias account which can be used to create new accounts and connect to existing accounts using OAuth2
* **TiresiasCipher** class for AES encrypting product/order information that can be exported for Tiresias to improve recommendations from the get-go
* **TiresiasDotEnv** class for handling environment variables used while developing and testing
* **TiresiasMessage** util class for holding info about messages that can be forwarded to the account administration iframe to show to the user
* **TiresiasObject** base class for Tiresias objects that need to share functionality
* **TiresiasOrderConfirmation** class for sending order confirmations through the API
* **TiresiasProductReCrawl** class for sending product re-crawl requests to Tiresias over the API
* **TiresiasValidator** class for performing data validations on objects implementing TiresiasValidatableInterface

### Interfaces

* **TiresiasAccountInterface** interface defining methods needed to manage Tiresias accounts
* **TiresiasAccountMetaDataBillingDetailsInterface** interface defining getters for billing information needed during Tiresias account creation over the API
* **TiresiasAccountMetaDataIframeInterface** interface defining getters for information needed by the Tiresias account configuration iframe
* **TiresiasAccountMetaDataInterface** interface defining getters for information needed during Tiresias account creation over the API
* **TiresiasAccountMetaDataOwnerInterface** interface defining getters for account owner information needed during Tiresias account creation over the API
* **TiresiasOrderBuyerInterface** interface defining getters for buyer information needed during order confirmation requests
* **TiresiasOrderInterface** interface defining getters for information needed during order confirmation requests
* **TiresiasOrderPurchasedItemInterface** interface defining getters for purchased item information needed during order confirmation requests
* **TiresiasOrderStatusInterface** interface defining getters for order status information needed during order confirmation requests
* **TiresiasExportCollectionInterface** interface defining getters for exportable data collections for the historical data
* **TiresiasOauthMetaDataInterface** interface defining getters for information needed during OAuth2 requests
* **TiresiasProductInterface** interface defining getters for product information needed during product re-crawl requests to Tiresias over the API
* **TiresiasValidatableInterface** interface defining getters for validatable objects that can be used in conjunction with the TiresiasValidator class

### Libs

* **TiresiasCryptAES** class for aes encryption that uses mcrypt if available and an internal implementation otherwise
* **TiresiasCryptBase** base class for creating encryption classes
* **TiresiasCryptRijndael** class for rijndael encryption that uses mcrypt if available and an internal implementation otherwise
* **TiresiasCryptRandom** class for generating random strings

## Getting started

### Creating a new Tiresias account

A Tiresias account is needed for every shop and every language within each shop.

```php
    .....
    try {
        /** @var TiresiasAccountMetaDataInterface $meta */
        /** @var TiresiasAccount $account */
        $account = TiresiasAccount::create($meta);
        // save newly created account according to the platforms requirements
        .....
    } catch (TiresiasException $e) {
        // handle failure
        .....
    }
    .....
```

### Connecting with an existing Tiresias account

This should be done in the shops back end when the admin user wants to connect an existing Tiresias account to the shop.

First redirect to the Tiresias OAuth2 server.

```php
    .....
    /** @var TiresiasOAuthClientMetaDataInterface $meta */
    $client = new TiresiasOAuthClient($meta);
  	header('Location: ' . $client->getAuthorizationUrl());
```

Then have a public endpoint ready to handle the return request.

```php
    if (isset($_GET['code'])) {
        try {
            /** @var TiresiasOAuthClientMetaDataInterface $meta */
            $account = TiresiasAccount::syncFromTiresias($meta, $_GET['code']);
            // save the synced account according to the platforms requirements
        } catch (TiresiasException $e) {
            // handle failures
        }
        // redirect to the admin page where the user can see the account configuration iframe
        .....
    }
    } elseif (isset($_GET['error'])) {
        // handle errors; 3 parameter will be sent, 'error', 'error_reason' and 'error_description'
        // redirect to the admin page where the user can see an error message
        .....
    } else {
        // 404
        .....
    }
```

### Deleting a Tiresias account

This should be used when you delete a Tiresias account for a shop. It will notify Tiresias that this account is no longer used.

```php
    try {
        /** @var TiresiasAccount $account */
        $account->delete();
    } catch (TiresiasException $e) {
        // handle failure
    }
```

### Get authenticated iframe URL for Tiresias account configuration

The Tiresias account can be created and managed through an iframe that should be accessible to the admin user in the shops
backend.
This iframe will load only content from tiresias.com.

```php
    .....
    /**
     * @var TiresiasAccount|null $account account with at least the 'SSO' token loaded or null if no account exists yet
     * @var TiresiasAccountMetaDataIframeInterface $meta
     * @var array $params (optional) extra params to add to the iframe url
     */
    try
    {
        $url = Tiresias::helper('iframe')->getUrl($meta, $account, $params);
    }
    catch (TiresiasException $e)
    {
        // handle failure
    }
    // show the iframe to the user with given url
    .....
```

The iframe can communicate with your module through window.postMessage
(https://developer.mozilla.org/en-US/docs/Web/API/Window/postMessage). In order to set this up you can include the JS
file `src/js/TiresiasIframe.min.js` on the page where you show the iframe and just init the API.

```js
    ...
    Tiresias.iframe({
        iframeId: "tiresias_iframe",
        urls: {
            createAccount: "url_to_the_create_account_endpoint_for_current_shop",
            connectAccount: "url_to_the_connect_account_endpoint_for_current_shop",
            deleteAccount: "url_to_the_delete_account_endpoint_for_current_shop"
        },
        xhrParams: {} // additional xhr params to include in the requests
    });
```

The iframe API makes POST requests to the specified endpoints with content-type `application/x-www-form-urlencoded`.
The response for these requests should always be JSON and include a `redirect_url` key. This url will be used to
redirect the iframe after the action has been performed. In case of the connect account, the url will be used to
redirect your browser to the Tiresias OAuth server.
The redirect url also needs to include error/success message keys, if you want to show messages to the user after the
actions, e.g. when a new account has been created a success message can be shown with instructions. These messages are
hard-coded in Tiresias.
You do NOT need to use this JS API, but instead set up your own postMessage handler in your application.

### Sending order confirmations using the Tiresias API

Sending order confirmations to Tiresias is a vital part of the functionality. Order confirmations should be sent when an
order has been completed in the shop. It is NOT recommended to do this when the "thank you" page is shown to the user,
as payment gateways work differently and you cannot rely on the user always being redirected back to the shop after a
payment has been made. Therefore, it is recommended to send the order conformation when the order is marked as payed
in the shop.

Order confirmations can be sent two different ways:

* matched orders; where we know the Tiresias customer ID of the user who placed the order
* un-matched orders: where we do not know the Tiresias customer ID of the user who placed the order

The Tiresias customer ID is set in a cookie "2c.cId" by Tiresias and it is up to the platform to keep a link between users
and the Tiresias customer ID. It is recommended to tie the Tiresias customer ID to the order or shopping cart instead of an
user ID, as the platform may support guest checkouts.

```php
    .....
    try {
        /**
         * @var TiresiasOrderInterface $order
         * @var TiresiasAccountInterface $account
         * @var string $customerId
         */
        TiresiasOrderConfirmation::send($order, $account, $customerId);
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

### Sending product re-crawl requests using the Tiresias API

Note: this feature has been deprecated in favor of the create/update/delete method below.

When a product changes in the store, stock is reduced, price is updated etc. it is recommended to send an API request
to Tiresias that initiates a product "re-crawl" event. This is done to update the recommendations including that product
so that the newest information can be shown to the users on the site.

Note: the $product model needs to include only `productId` and `url` properties, all others can be omitted.

```php
    .....
    try {
        /**
         * @var TiresiasProductInterface $product
         * @var TiresiasAccountInterface $account
         */
        TiresiasProductReCrawl::send($product, $account);
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

Batch re-crawling is also possible by creating a collection of product models:

```php
    .....
    try {
        /**
         * @var TiresiasExportProductCollection $collection
         * @var TiresiasProductInterface $product
         * @var TiresiasAccountInterface $account
         */
        $collection[] = $product;
        TiresiasProductReCrawl::sendBatch($collection, $account);
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

### Sending product create/update/delete requests using the Tiresias API

When a product changes in the store, stock is reduced, price is updated etc. it is recommended to send an API request
to Tiresias to handle the updated product info. This is also true when adding new products as well as deleting existing ones.
This is done to update the recommendations including that product so that the newest information can be shown to the users
on the site.

Creating new products:

```php
    .....
    try {
        /**
         * @var TiresiasProductInterface $product
         * @var TiresiasAccountInterface $account
         */
        $op = new TiresiasOperationProduct($account);
        $op->addProduct($product);
        $op->create();
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

Note: you can call `addProduct` multiple times to add more products to the request. This way you can batch create products.

Updating existing products:

```php
    .....
    try {
        /**
         * @var TiresiasProductInterface $product
         * @var TiresiasAccountInterface $account
         */
        $op = new TiresiasOperationProduct($account);
        $op->addProduct($product);
        $op->update();
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

Note: you can call `addProduct` multiple times to add more products to the request. This way you can batch update products.

Deleting existing products:

```php
    .....
    try {
        /**
         * @var TiresiasProductInterface $product
         * @var TiresiasAccountInterface $account
         */
        $op = new TiresiasOperationProduct($account);
        $op->addProduct($product);
        $op->delete();
    } catch (TiresiasException $e) {
        // handle error
    }
    .....
```

Note: you can call `addProduct` multiple times to add more products to the request. This way you can batch delete products.

### Exporting encrypted product/order information that Tiresias can request

When new Tiresias accounts are created for a shop, Tiresias will try to fetch historical data about products and orders.
This information is used to bootstrap recommendations and decreases the time needed to get accurate recommendations
showing in the shop.

For this to work, Tiresias requires 2 public endpoints that can be called once a new account has been created through
the API. These endpoints should serve the history data encrypted with AES. The SDK comes bundled with the ability to
encrypt the data with a pure PHP solution (http://phpseclib.sourceforge.net/), It is recommended, but not required, to
have mcrypt installed on the server.

Additionally, the endpoints need to support the ability to limit the amount of products/orders to export and an offset
for fetching batches of data. These must be implemented as GET parameters "limit" and "offset" which will be sent as
integer values and expected to be applied to the data set being exported.

```php
    .....
    /**
     * @var TiresiasProductInterface[] $products
     * @var TiresiasAccountInterface $account
     */
    $collection = new TiresiasExportProductCollection();
    foreach ($products as $product) {
        $collection[] = $product;
    }
    // The exported will encrypt the collection and output the result.
    $cipher_text = TiresiasExporter::export($account, $collection);
    echo $cipher_text;
    // It is important to stop the script execution after the export, in order to avoid any additional data being outputted.
    die();
```

```php
    .....
    /**
     * @var TiresiasOrderInterface[] $orders
     * @var TiresiasAccountInterface $account
     */
    $collection = new TiresiasExportOrderCollection();
    foreach ($orders as $order) {
        $collection[] = $order;
    }
    // The exported will encrypt the collection and output the result.
    $cipher_text = TiresiasExporter::export($account, $collection);
    echo $cipher_text;
    // It is important to stop the script execution after the export, in order to avoid any additional data being outputted.
    die();
```

## Testing

The SDK is unit tested with Codeception (http://codeception.com/).
API and OAuth2 requests are tested using api-mock server (https://www.npmjs.com/package/api-mock) running on Node.

### Install Codeception & api-mock

First cd into the root directory.

Then install Codeception via composer:

```bash
    php composer.phar install
```

And then install Node (http://nodejs.org/) and the npm package manager (https://www.npmjs.com/). After that you can install the api-mock server via npm:

```bash
    npm install -g api-mock
```

### Running tests

First cd into the root directory.

Then start the api-mock server with the API blueprint:

```bash
    api-mock tests/api-blueprint.md
```

Then in another window run the tests:

```bash
    vendor/bin/codecept run
```
