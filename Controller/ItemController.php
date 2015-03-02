<?php

namespace tfk\telemarkBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use Pagerfanta\Pagerfanta;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\API\Repository\Values\Content\Location;

class ItemController extends Controller {

    public function folderAction($locationId)
    {

        // Setting HTTP cache for the response to be public and with a TTL of 1 day.
        $response = new Response;
        $response->setPublic();
        $response->setSharedMaxAge( 86400 );
        // Menu will expire when top location cache expires.
        $response->headers->set( 'X-Location-Id', $locationId );
        // Menu might vary depending on user permissions, so make the cache vary on the user hash.
        $response->setVary( 'X-User-Hash' );

        $location = $this->getRepository()->getLocationService()->loadLocation( $locationId );
        $content = $this->getRepository()->getContentService()->loadContentByContentInfo( $location->getContentInfo() );

        $searchService = $this->getRepository()->getSearchService();
        $query = new Query();

        $query->criterion = new Criterion\LogicalAnd( array(
                new Criterion\ContentTypeIdentifier( array("article")),
                new Criterion\ParentLocationId($locationId),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE )
            ) );
        $query->sortClauses = array(
            new SortClause\LocationPriority( Query::SORT_DESC ),
            new SortClause\DatePublished( Query::SORT_DESC )
        );

        // Initialize pagination.
        $items = new Pagerfanta(
            new ContentSearchAdapter( $query, $this->getRepository()->getSearchService() )
        );
        $items->setMaxPerPage( 15 );
        $items->setCurrentPage( $this->getRequest()->get( 'page', 1 ) );

        /*$repository = $this->getRepository();
        $location = $repository->getLocationService()->loadLocation( $locationId );
        $serieContent = $repository->getContentService()->loadContent($location->contentInfo->id);
        $results = $this->getRepository()->getLocationService()->loadLocationChildren( $currentLocation );
        $itemsList = array();

        foreach ( $results->locations as $result ) {

            $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $result->contentInfo->mainLocationId );
            $content = $this->getRepository()->getContentService()->loadContent($currentLocation->contentInfo->id);
            if ($result->hidden == false)
                $itemsList[] = $content;
        }*/

        return $this->render(
            'tfktelemarkBundle:full:folder.html.twig', 
            array( 
                'items' => $items,
                'location' => $location,
                'content' => $content
            ), $response );
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