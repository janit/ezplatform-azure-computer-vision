# eZ Platform Azure Computer Vision Integration PoC
A proof of concept of integrating eZ Platform with Microsoft Azure Computer Vision API to provide automatic image captions.

This repository contains an eZ Platform 3.0 compatible Event Subscriber that talks to the Azure REST API using the Symfony HTTP Client. There is no support or documentation available, aside from the inline comments. To enable this Event Subscriber you should make sure it is configured appropriately in config/services.yaml as described in <a href="https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber">the Symfony Event Dispatcher documentation</a>.

More information in the blog post: <a href="https://ezplatform.com/blog/automatic-image-captions-with-microsoft-azure-computer-vision-api">Automatic image captions with Microsoft Azure Computer Vision API</a>

## eZ Platform is now Ibexa DXP

Going forward from version 3.2 eZ Platform (Enterprise Edition) will be known as the [Ibexa DXP technology](https://www.ibexa.co/products) that is the base for three products: [Ibexa Content](https://www.ibexa.co/products/ibexa-content), [Ibexa Experience](https://www.ibexa.co/products/ibexa-experience) and [Ibexa Commerce](https://www.ibexa.co/products/ibexa-commerce). Instructions in this code should be relevant since Ibexa DXP is an evolution of eZ Platform, not a revolution. Learn more from the [Ibexa DXP v3.2 launch post](https://www.ibexa.co/blog/product-launch-introducing-ibexa-dxp-3.2) and the [Ibexa developer portal](https://developers.ibexa.co).
