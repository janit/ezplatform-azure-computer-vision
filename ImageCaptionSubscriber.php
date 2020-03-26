<?php

/*
 * ezplatform-azure-computer-vision
 *
 * This is an eZ Platform v3.0 compatible event subscriber that automatically
 * populates image objects' caption field with a caption field provided by
 * the Microsoft Azure Computer Vision API:
 *  - https://azure.microsoft.com/en-us/services/cognitive-services/computer-vision/
 *
 * eZ Platform is built on the Symfony Framework. To enable this Event Subscriber
 * you should make sure it is configured appropriately in config/services.yaml
 * - https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
 *  
 * More details in the blog article here:
 *  - https://ezplatform.com/blog/automatic-image-captions-with-microsoft-azure-computer-vision-api
 *
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\KernelEvents;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;

class ImageCaptionSubscriber implements EventSubscriberInterface
{

    const IMAGE_CLASS_IDENTIFIER = 'image';
    const IMAGE_FIELD_IDENTIFIER = 'image';
    const CAPTION_FIELD_IDENTIFIER = 'caption';
    const SERVICE_ENDPOINT = 'https://westcentralus.api.cognitive.microsoft.com/vision/v2.1/analyze?visualFeatures=Description&language=en';

    // Get your key from here: https://docs.microsoft.com/en-us/azure/cognitive-services/computer-vision/quickstarts/curl-analyze#prerequisites
    const SERVICE_KEY = 'YOUR-PERSONAL-KEY-HERE';

    protected $contentTypeService;
    protected $contentService;
    protected $locationService;
    protected $permissionResolver;
    protected $userService;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        UserService $userService
    ){
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Subscribe for event when a content item is published
            PublishVersionEvent::class => 'onPublishVersion'
        ];
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {

        $contentObject = $event->getContent();

        // Only get automatic captions for IMAGES and first version
        if(
            $contentObject->contentType->identifier === SELF::IMAGE_CLASS_IDENTIFIER
            AND $event->getVersionInfo()->versionNo === 1
        ){

            $imageFieldValue = $contentObject->getFieldValue(SELF::IMAGE_FIELD_IDENTIFIER);
            $imageUrl = 'https://' . $_SERVER['SERVER_NAME'] . $imageFieldValue->uri;
            $caption = $this->getCaptionForImage($imageUrl);
            $this->updateContentObject($contentObject, $caption);

        }
    }

    // Based on: https://docs.microsoft.com/en-us/azure/cognitive-services/computer-vision/quickstarts/curl-analyze
    function getCaptionForImage(string $imageUrl): string {

        $client = HttpClient::create();

        $response = $client->request('POST', SELF::SERVICE_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => SELF::SERVICE_KEY
            ],
            'body' => '{"url":"' . $imageUrl .  '"}'
        ]);

        $imageDetails = json_decode($response->getContent());

        $caption = $imageDetails->description->captions[0]->text;

        return $caption;

    }

    // Updates content object using eZ Platform API
    // Learn more: https://doc.ezplatform.com/en/latest/api/public_php_api_creating_content/
    function updateContentObject($contentObject, $caption){

        $richTextInput = <<< RICHTEXT_TEMPLATE
        <?xml version="1.0" encoding="UTF-8"?>
        <section xmlns="http://ez.no/namespaces/ezpublish5/xhtml5/edit">
            <p>$caption</p>
        </section>
        RICHTEXT_TEMPLATE;

        try {

            $contentInfo  = $this->contentService->loadContentInfo($contentObject->id);
            $contentDraft = $this->contentService->createContentDraft( $contentInfo );

            $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
            $contentUpdateStruct->initialLanguageCode = 'eng-GB';

            $contentUpdateStruct->setField(SELF::CAPTION_FIELD_IDENTIFIER, $richTextInput);

            $draft = $this->contentService->updateContent($contentDraft->versionInfo, $contentUpdateStruct);
            $content = $this->contentService->publishVersion($draft->versionInfo);

        } catch (\Exception $e){
            dump($e);
        }

    }

}