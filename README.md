# Personalization for PrestaShop

Increase your conversion rate and average order value by delivering your customers personalized product recommendations
throughout their shopping journey.

Tiresias allows you to deliver every customer a personalized shopping experience through recommendations based on their
unique user behavior - increasing conversion, average order value and customer retention as a result.

[http://betechnology.es](http://betechnology.com/)

## Getting started

### How it works

The extension automatically adds product recommendation elements to the shop when installed. Basically, empty "div"
placeholder elements. These elements will appear on the home page, product pages, category pages, search result pages
and the shopping cart page. These elements are automatically populated with product recommendations from your shop.

This is possible by mining data from the shop when the user visits the pages. For example, when the user is browsing a
product page, the product information is asynchronously sent to Tiresias, that in turn delivers product recommendations
based on that product to the shop and displays them to the user.

The more users that are visiting the site, and the more page views they create, the better and more accurate the
recommendations become.

In addition to the recommendation elements and the real time data gathering, the extension also includes some behind the
scenes features for keeping the product information up to date and keeping track of orders in the shop.

Every time a product is updated in the shop, e.g. the price is changed, the information is sent to Tiresias over an API.
This will sync the data across all the users visiting the shop that will see up to date recommendations.

All orders that are placed in the shop are also sent to Tiresias. This is done to keep track of the orders that were a
direct result of the product recommendations, i.e. when a user clicks a product in the recommendation, adds it to the
shopping cart and places the order.

Tiresias also keeps track of the order statuses, i.e. when an order is changed to "payed" or "canceled" the order is
updated over an API.

All you need to take Tiresias into use in your shop, is to install the extension and create a Tiresias account for your
shop. This is as easy as clicking a button, so read on.

### Installing

The module comes bundled with PrestaShop 1.5 and 1.6. For PrestaShop 1.4 you wll need to fetch the module archive from
the [addons page](http://addons.prestashop.com/en/advertising-marketing-newsletter-modules/18349-Tiresiastagging.html) and
upload the archive in the backend of your PrestaShop installation.

To install the module in PrestaShop, navigate to the "Modules" section in the backend and locate the module under the
"Advertising & Marketing" section. Then just click the "Install" button next to the module in the list. That's it.

### Configuration

By clicking the modules "Configure" link in the modules listing, you will be redirected to the Tiresias account
configuration page were you can create and manage your Tiresias accounts. You will need a Tiresias account for each shop
(multi-shop) and language in the installation.

Creating the account is as easy as clicking the install button on the page. Note the email field above it. You will need
to enter your own email to be able to activate your account. After clicking install, the window will refresh and show
the account configuration.

You can also connect and existing Tiresias account to a shop, by using the link below the install button. This will take
you to Tiresias where you choose the account to connect, and you will then be redirected back where you will see the same
configuration screen as when having created a new account.

This concludes the needed configurations in PrestaShop. Now you should be able to view the default recommendations in
your shops frontend by clicking the preview button on the page.

You can read more about how to modify Tiresias to suit your needs in our [support center](https://support.betechnology.com/),
where you will find PrestaShop related documentation and guides.

### Extending

#### Change position of recommendation elements

All recommendations are added through the PrestaShop hook system, which means that their position is dependent on the
hooks position in the theme.

In order to change the position of any recommendation element added by Tiresias, you can either move the PrestaShop hook
position in your theme or unlink the Tiresias module from the hook and add the elements directly into your theme. Please
refer to the PrestaShop documentation on how to re-position hooks.

During the module installation, some new hooks are also created. These are:

* displayCategoryTop
* displayCategoryFooter
* displaySearchTop
* displaySearchFooter

These can be used to position the recommendations on the product category and search result pages, as PrestaShop does
not include any hooks out-of-the-box for these pages. The module will automatically add the recommendations without
these hooks as well, but for more precise positioning we recommend to include them in your theme. This is as easy as
adding a line like `{hook h='displayCategoryTop'}` to your theme layout file.

#### Adding new recommendation elements

The easiest way to add your own recommendation elements is to simply add the placeholder "div" in your theme layout,
e.g. `<div class="Tiresias_element" id="{id-of-your-choice}"></div>`. Note that you need to register this new element in
your [Tiresias account settings](https://my.Tiresias.com/), so that Tiresias can start using it.

## License

Academic Free License ("AFL") v. 3.0

## Dependencies

PrestaShop version 1.4.x - 1.6.x


## ¿Como se inicia la instalacion?

Todos los hooks se instalan en el metodo constructor de la clase tiresiastagging.php. Cada hook esta asociado con un fichero tpl 
Activacion del addon. La clase que contiene el boton de Instalar es config-bootstrap.tpl 

## ¿Como se emiten los eventos?

Campos:  
1. c -> Id del cliente este logado o no  
2. m-> Id del market o tieda. Se gener uno nuevo en cada instalacion  
3. data -> ev: array:  

* el: array con los posibles huecos.
* cats: array de las categorias de los elementos mostrados.
* oc: (true, false) -> false en el frontpage y en la pagina de nosto-page-category.
* cids: codigo del producto del carrito.
* cs -> Cart selection -> Nº de elementos del carrito.
* ct -> Cart total -> Total del precio de los items.

4. cb -> Funcion callback a la que hay que llamar de vuelta  


## ¿Como se inicia una consulta de recomendaciones?

Todo empieza en la clase header_embed-script.tpl que invoca a la url de la API que devolverá el script JavaScript que invocará a su vez a la función de invocaciones de eventos "ev1".

header_embed-script.tpl -> (API) include -> (function) invoke ev1 -> (API) ev1

## ¿Como se muestran las recomendaciones?

Campos:  
* cs -> Cart selection -> Nº de elementos del carrito
* ct -> Cart total -> Total del precio de los items
* customer -> Id del cliente este logado o no
* errors -> array de si ha habido errores
* he -> (false, true) ->
* hiic -> (false, true) ->
* pv -> Nº de recomendaciones realizadas

Campos Push:  
* pv -> Tiene el numero total de recomendaciones emitidas.
* el -> elements -> Elementos de la pagina mostrada donde se pueden poner recomendaciones

En la pagina principal se muestran recomendaciones con el nombre de 'frontpage-nosto-1'
En la pagina de categorias se muestran recomendaciones con el nombre de 'nosto-page-category1'
En la pagina de producto se muestran recomendaciones con el nombre de 'nosto-page-product1'
En la pagina del carrito se muestran recomendaciones con el nombre de 'nosto-page-cart1'
En la pagina de busqueda se muestran recomendaciones con el nombre de 'nosto-page-search1'


## Modo preview

Para poder previsualizar las recomendaciones en modo debug, el controlador recibe el parametro tiresiasdebug=true y id_lang=1 e invoca a la API de include donde se lanzan dos javascripts. Uno de Jquery y otro con la toolbar. Esta se llama debugtoolbar.min.js y tiene toda la logica de mostrar el debug.
Hay que fijarse que tambien se muestra informacion de carga en la consola javascript de absolutamente todo.

## Changelog

* 2.4.3
    * Update module admin page

* 2.4.2
    * Fixed issue with missing "addCss" method in hook "displayBackOfficeHeader", when controller in context inherits
    from "AdminTab" instead of "AdminController" in PS 1.5+

* 2.4.1
    * Fixed bad release package

* 2.4.0
    * Added Tiresias admin tab to PS 1.5 and 1.6 versions for easy access to the Tiresias admin pages
    * Added product attribute combinations to the product name in cart and order tagging to easily recognise them
    * Added order status and payment provider info to order tagging
    * Added support for account specific sub-domains when configuring Tiresias

* 2.3.0
    * Added the new product update API and removed deprecated product re-crawl
    * Added order status information to the order confirmation API
    * Added feature to add product to cart directly from recommendations
    * Refactored data model validation and allow incomplete data to be tagged in frontend
    * Updated Tiresias SDK

* 2.2.1
    * Fixed connecting existing Tiresias account using OAuth for multi-shop setups in Prestashop 1.5.0.0 - 1.5.4.1
    * Fixed preview urls on module admin page for multi-shop setups in Prestashop 1.5.0.0 - 1.5.4.1

* 2.2.0
    * New module admin UI
    * Added hook for sending new product data to Tiresias right after product has been created
    * Added tagging for search terms
    * Improved OAuth error messages
    * Fixed product price tagging tax display to depend on user active group
    * Fixed product availability tagging to also depend on the products visibility in PS 1.5+

* 2.1.1
    * Fix SDK sub-repository to use https instead of ssh

* 2.1.0
    * Improve server-to-server order confirmations
    * Implement all communication with Tiresias through the Tiresias SDK library

* 2.0.1
    * Fixed cart tagging to show also when cart is empty

* 2.0.0
    * Updated translations

* 1.3.7
    * Fixed customer tagging for PS 1.4 versions

* 1.3.6
    * Added http request adapter for curl
    * Prestashop standards-compliant

* 1.3.5
    * Fixed check for current controller in some PS 1.5 versions

* 1.3.4
    * Fixed issue with logged in customer tagging on PS 1.4
    * Fixed recommendation slot logic to check the whole document and not only the center column before adding defaults

* 1.3.3
    * Fixed issue with logged in customer tagging

* 1.3.2
    * Fixes

* 1.3.1
    * Fixed issue with front page url on account creation for shops without clean urls

* 1.3.0
    * Added support for multi-shop installations
    * Added support for multi-lingual installations
    * Added OAuth2 authorization for connecting an existing Tiresias account
    * Added SSO support for logging in to Tiresias
    * Added support for tagging brand pages (manufacturer)
    * Added PS 1.4 support
    * Added support for re-crawling products
    * Disabled the left/right column hooks
    * Updated localizations for English, French, German and Spanish
    * Updated module admin page UI

* 1.2.0
    * Added option to create a new Tiresias account in module settings, or use an existing one
    * Added support for basic recommendation slot editor in module settings (Prestashop 1.6 only)
    * Added support for sending order confirmations to Tiresias's REST API without having the Tiresias customer-id link
    * Added support for getting product and order history from the shop
    * Fixed bug in Tiresias account creation where the employee first name was put as last name and vice versa
    * Fixed structure of order confirmations in REST API calls

* 1.1.2
    * Fixed category page tagging for Prestashop versions 1.5.x => 1.5.5.0

* 1.1.1
    * Fixed issue with category not always being passed to hook "hookDisplayFooterProduct"

* 1.1.0
    * Added current category to the product page tagging
    * Added product tags to the product page tagging
    * Added French translations
    * Added Tiresias icon and improved the description
    * Added support for actionPaymentConfirmation hook to push order confirmations to Tiresias's REST API
    * Added support for automatic account creation on module install
    * Added configuration option for automatically injecting recommendation slots on the category and search pages
    * Fixed initial installation error
    * Fixed category tagging to display only active and visible categories
    * Fixed broken product image URL when using the legacy image filesystem
    * Removed the "Top Sellers" page
    * Removed "Server Address" from module configuration
    * Updated Tiresias JavaScript to the newest version
    * Updated the admin user interface for Prestashop 1.6

* 1.0.0
	* Initial release
