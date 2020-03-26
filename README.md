# eZ Platform Azure Computer Vision Integration PoC
A proof of concept of integrating eZ Platform with Microsoft Azure Computer Vision API to provide automatic image captions.

This repository contains an eZ Platform 3.0 compatible Event Subscriber that talks to the Azure REST API using the Symfony HTTP Client. There is no support or documentation available, aside from the inline comments. To enable this Event Subscriber you should make sure it is configured appropriately in config/services.yaml as described in <a href="https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber">the Symfony Event Dispatcher documentation</a>.

More information in the blog post: <a href="https://ezplatform.com/blog/automatic-image-captions-with-microsoft-azure-computer-vision-api">Automatic image captions with Microsoft Azure Computer Vision API</a>





