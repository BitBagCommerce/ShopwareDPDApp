## Installation

You can install the app in a variety of ways.

1. Install the app from [the Shopware store](https://store.shopware.com/en/extensions/?p=1&o=12&n=21&c=2069&shopwareVersion=6)
2. Install the app from [the back office](https://docs.shopware.com/en/shopware-6-en/extensions/myextensions)

3. Install the app from the command line
    1. Create directory inside custom/app called BitBagShopwareDPDApp
    2. In the new directory put the manifest.xml file
    3. Install the app
       ```bash
       $ bin/console app:install BitBagShopwareDPDApp
       ```
    4. Activate the plugin
       ```bash
         $ bin/console app:activate BitBagShopwareDPDApp
       ```

## Deploy Symfony app on server
- [Symfony deploy](https://symfony.com/doc/current/deployment.html)
