<?php

namespace tfk\telemarkBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use Pagerfanta\Pagerfanta;

class ItemController extends Controller {
	
	 public function childrenAction($locationId, $params = array()) {
		// children
        // Setting HTTP cache for the response to be public and with a TTL of 1 day.
        $response = new Response;
		 
        $response->setPublic();
        $response->setSharedMaxAge( 86400 );
        // Menu will expire when top location cache expires.
        $response->headers->set( 'X-Location-Id', $locationId );
        // Menu might vary depending on user permissions, so make the cache vary on the user hash.
        $response->setVary( 'X-User-Hash' );
		 
        $location  = $this->getRepository()->getLocationService()->loadLocation( $locationId );
        $content = $this->getRepository()->getContentService()->loadContentByContentInfo( $location->getContentInfo() );

        $searchService = $this->getRepository()->getSearchService();
        $query = new Query();	 
        $query->criterion = new Criterion\LogicalAnd( array(
                new Criterion\ContentTypeIdentifier($params['class']),
                new Criterion\ParentLocationId($locationId),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE )
            ) );
        $query->sortClauses = array(
            new SortClause\LocationPriority( Query::SORT_DESC ),
            new SortClause\DatePublished( Query::SORT_DESC )
        );

        $items = array();
        $result = $searchService->findContent( $query );
        if ($result->totalCount > 0) {
            foreach ($result->searchHits as $item) {
                $itemLoc  = $this->getRepository()->getLocationService()->loadLocation( $item->valueObject->contentInfo->mainLocationId );
                if (!$itemLoc->invisible)
                    $items[] = $item->valueObject;
            }
        }
		
        return $this->render(
            'tfktelemarkBundle:parts:child_loop.html.twig', 
            array( 
                'items' => $items,
                'location' => $location,
                'content' => $content,
				'viewType' => $params['viewType']
            ), $response );
    }
	
	 public function mainMenuAction($locationId) {
		// topp meny controller
		 
        $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $locationId );
        $results = $this->getRepository()->getLocationService()->loadLocationChildren( $currentLocation );
        $locationList = array();
        $subLocationList = array();
        foreach ( $results->locations as $result ) {

            $currentLocation = $this->getRepository()->getLocationService()->loadLocation( $result->contentInfo->mainLocationId );
            $content = $this->getRepository()->getContentService()->loadContent($currentLocation->contentInfo->id);

            if ($content->getFieldValue('hide_from_main_menu') == '0') {
                $locationList[] = $currentLocation;
                $subresults = $this->getRepository()->getLocationService()->loadLocationChildren( $currentLocation );
                foreach ($subresults->locations as $subresult) {
                    if (!empty($subresult)) {
                        $subLocationList[$result->contentInfo->mainLocationId][] = $this->getRepository()->getLocationService()->loadLocation( $subresult->contentInfo->mainLocationId );
                    }
                }
            }


        }
        return $this->render('tfktelemarkBundle:parts:menu_main.html.twig', array( 'list' => $locationList, 'sublist' => $subLocationList) );
    }
	
    public function arkivAction($locationId)
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
		
 		if ($content->getFieldValue('show_children') == '1') {
			
        $query->criterion = new Criterion\LogicalAnd( array(
                new Criterion\ContentTypeIdentifier( array('article','linkbox')),
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
        $items->setMaxPerPage( 9 );
        $items->setCurrentPage( $this->getRequest()->get( 'page', 1 ) );
			
        return $this->render(
            'tfktelemarkBundle:full:folder_arkiv.html.twig', 
            array( 
                'items' => $items,
                'location' => $location,
                'content' => $content
            ), $response );
		}
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