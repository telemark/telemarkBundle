<?php

namespace tfk\telemarkBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;

class ItemController extends Controller {

    public function folderAction( $locationId)
    {

        // Setting HTTP cache for the response to be public and with a TTL of 1 day.
        $response = new Response;
        $response->setPublic();
        $response->setSharedMaxAge( 86400 );
        // Menu will expire when top location cache expires.
        $response->headers->set( 'X-Location-Id', $locationId );
        // Menu might vary depending on user permissions, so make the cache vary on the user hash.
        $response->setVary( 'X-User-Hash' );

        $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $locationId );
        $results = $this->getRepository()->getLocationService()->loadLocationChildren( $currentLocation );
        $itemsList = array();

        foreach ( $results->locations as $result ) {

            $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $result->contentInfo->mainLocationId );
            $content = $this->getRepository()->getContentService()->loadContent($currentLocation->contentInfo->id);
            if ($result->hidden == false)
                $itemsList[] = $content;
        }
        return $this->render('tfktelemarkBundle:parts:folder_loop.html.twig', array( 'items' => $itemsList), $response );
    }

    public function menuAction( $locationId)
    {

        // Setting HTTP cache for the response to be public and with a TTL of 1 day.
        $response = new Response;
        $response->setPublic();
        $response->setSharedMaxAge( 86400 );
        // Menu will expire when top location cache expires.
        $response->headers->set( 'X-Location-Id', $locationId );
        // Menu might vary depending on user permissions, so make the cache vary on the user hash.
        $response->setVary( 'X-User-Hash' );

        $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $locationId );
        $results = $this->getRepository()->getLocationService()->loadLocationChildren( $currentLocation );
        $itemsList = array();
        foreach ( $results->locations as $result ) {

            $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $result->contentInfo->mainLocationId );
            $content = $this->getRepository()->getContentService()->loadContent($currentLocation->contentInfo->id);
            $itemsList[] = $content;
        }
        return $this->render('tfktelemarkBundle:parts:menu_loop.html.twig', array( 'items' => $itemsList), $response );
    }

}