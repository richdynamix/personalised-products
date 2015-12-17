# Personalised Products for Magento 2

[![Codacy Badge](https://api.codacy.com/project/badge/grade/a3a65aaab04249468edbac783c5ae16d)](https://www.codacy.com/app/steven_4/personalised-products) [![Build Status](https://scrutinizer-ci.com/g/richdynamix/personalised-products/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/richdynamix/personalised-products/build-status/develop) [![Quality Score](https://scrutinizer-ci.com/g/richdynamix/personalised-products/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/richdynamix/personalised-products/build-status/develop)

Personalised Products is a Magento 2 module that will serve realtime predicted suggestions for product upsells on the product page and complimentary suggestions for cross sells on the basket page. All powered by PredictionIO.

Using the two individual prediction engines the module will serve accurate and popular products to the customers based on the viewing and buying actions of other users.

##### Looking for the Magento 1.x version?
[Similar-Products](https://github.com/richdynamix/Similar-Products "Similar-Products") was the original PredictionIO Magento module however it's was designed around an older version of PredictionIO and will not work with the latest. Magento Hackathon has forked it and done some work with PredictionIO `v0.8` You can have a look at that version [here](https://github.com/magento-hackathon/Predictionio, "here")

### Requirements

- [PredictionIO](https://prediction.io/ "PredictionIO") +0.9.5
- [Complementary Purchase](https://templates.prediction.io/PredictionIO/template-scala-parallel-complementarypurchase "Complementary Purchase") engine template v0.3.3
- [Similar Product](https://templates.prediction.io/PredictionIO/template-scala-parallel-similarproduct "Similar Product") engine template v0.3.2
- [Magento 2.0](https://www.magentocommerce.com/download, "Magento 2.0")

## Installation

Go to the root folder of your Magento 2 installation and include the composer package.

```BASH
$ composer require richdynamix/personalised-products
```

Run the setup scripts

```BASH
$ bin/magento setup:upgrade
```

Clear all caches and code generation folder

```BASH
$ rm -fr var/cache
```
```BASH
$ rm -fr var/page_cache/
```
```BASH
$ rm -fr var/generation/
```

## Configuration

Once you have your PredictionIO installation complete, an event server listening for data and the two prediction engines built, trained and deployed you can start using the features of the module.

Add your app access key into the module configuration found at `Stores->Configuration->Richdynamix->Personalised Products->General`. If you are not sure where to obtain your access key simple run `pio app list` on the the server where you installed PredictionIO.

Your app access key will be similar to `yLr9skhuvbKE7vI6TpkJv6sTDpozUBA1ZZkiam1l1cscMr7yXlBePk2pRNVVID7i`

Next add your URL and port to the event server. By default this is port `7070` however you can simply change this in your installation should the port conflict with another service. The event server URL can be either a FQDN or an IP address. If you do not supply a HTTP scheme then the module will default to `http://`

Similar to the event server you must supply the URL for the built prediction engine. By default the port is `8000` however there will be obvious clashes with the two engines servers so please change one of these during deployment. For information on how to configure the port please see the PredictionIO docs [here](https://docs.prediction.io/deploy/#specify-a-different-engine-port "here")

For each of the engines you can supply a product count. This is the number of products that should be returned from the engine. Additionally, for the similarity engine your can choose to only return items from within the same category as the product you are viewing. This will help with more focused results.

## Data Input

Customers are added to the event server when they first register on the site. To populate your event server with existing customers please see the console command below.

When you add a new product to the site the product is put into a queue table ready for export, there is a cron schedule setup to process products in the queue every hour and push new products to the PredictionIO event server. To populate your event server with existing products please see the console command below.

As the customers browse the site the module will record the product view action in your PredictionIO event server. Since the action of user-view-item requires a customer ID we can only do this when the customer is logged in. When the customer is not logged in, we record the products viewed using a cookie and push to PredictionIO when the customer does login.

When the customer creates a new order each of the items in the basket are sent to your PredictionIO event server with the user-buy-item action. 
_This action event is only recorded for orders where the customer has an account_

## Data Output

On product pages the products upsell block will get the product collection from the returned dataset in PredictionIO. This is an ordered list or product ID’s based on the predicted score of those products. This collection of product ID’s is then used to retrieve a new collection for the upsells.

The basket page cross sells works in a a similar way to product page upsells. The only real difference is you cannot filter those results by category. Instead, the complementary engine will use a list of all products in the basket and return a scored ordered list of products to display. If products in the returned list are already present in the basket then these are removed from the returned collection.

## Console Commands

To kick start your data exports there are three console commands you can use.

- `$ bin/magento pio:send:customers`
- `$ bin/magento pio:send:products`
- `$ bin/magento pio:send:orders`

### $ bin/magento pio:send:customers

Running the customers command will export all customers in the Magento store, regardless if they have been exported before or which individuals store they belong to.

### $ bin/magento pio:send:products

All products with a visibility set to catalog/search are exported to the event server. Like the customers command this is regardless if the products have been exported before and regardless of the individual Magento store.

### $ bin/magento pio:send:orders

The orders command will filter out all current orders in the Magento store where the customer ID is `NOT NULL`. This ensures all `user-buy-item` events have a valid customer ID to record the action. There is an individual `user-buy-item` event on each product in the order.

## Author

Steven Richardson - [@mage_gizmo](https://twitter.com/mage_gizmo "@mage_gizmo")














