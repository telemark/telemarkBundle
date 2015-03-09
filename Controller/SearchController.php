<?php
/**
 * File containing the SearchController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace tfk\telemarkBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use EzSystems\DemoBundle\Entity\SimpleSearch;
use EzSystems\DemoBundle\Helper\SearchHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use Pagerfanta\Pagerfanta;

class SearchController extends Controller
{
    /**
     * Displays the simple search page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showSearchResultsAction( Request $request )
    {
        $response = new Response();
        $searchText = "";
        $searchCount = 0;

        // Creating a form using Symfony's form component
        $simpleSearch = new SimpleSearch();
        $form = $this->createForm( $this->get( 'ezdemo.form.type.simple_search' ), $simpleSearch );
        $form->handleRequest( $request );

        $pager = null;

        if ( $form->isValid() )
        {
            /** @var SearchHelper $searchHelper */
            $searchHelper = $this->get( 'ezdemo.search_helper' );

            if ( !empty( $simpleSearch->searchText ) )
            {
                $searchText = $simpleSearch->searchText;

                $pager = $searchHelper->searchForPaginatedContent(
                    $searchText,
                    $request->get( 'page', 1 ),
                    $this->getConfigResolver()->getParameter( 'languages' )
                );

                $searchCount = $pager->getNbResults();
            }
        }

        return $this->render(
            'tfktelemarkBundle:search:search.html.twig',
            array(
                'searchText' => $searchText,
                'searchCount' => $searchCount,
                'pagerSearch' => $pager,
                'form' => $form->createView()
            ),
            $response
        );
    }

    /**
     * Displays the search box for the page header
     *
     * @return Response HTML code of the page
     */
    public function searchBoxAction()
    {
        $response = new Response();

        $simpleSearch = new SimpleSearch();
        $form = $this->createForm( $this->get( 'ezdemo.form.type.simple_search' ), $simpleSearch );

        return $this->render(
            'tfktelemarkBundle::page_header_searchbox.html.twig',
            array(
                'form' => $form->createView()
            ),
            $response
        );
    }

    public function searchAction() {
        $request = Request::createFromGlobals();
        $searchString = $request->query->get('s');

        /*$query = new Query();
     
        $query->query = new Criterion\FullText( $searchString );
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\Visibility( Criterion\Visibility::VISIBLE )
            )
        );

        // Initialize pagination.
        $items = new Pagerfanta(
            new ContentSearchAdapter( $query, $this->getRepository()->getSearchService() )
        );
        $items->setMaxPerPage( 50 );
        $items->setCurrentPage( $this->getRequest()->get( 'page', 1 ) );

        $searchCount = $items->getNbResults();*/


        $result = $this->getLegacyKernel()->runCallback(
            function () use( $searchString )
            {      
                 return \eZFunctionHandler::execute(
                    'ezfind', 
                    'search', 
                    array(
                        'query' => $searchString,
                        'section_id' => 1,
                        'limit' => 50
                    )
                );
            }
        );

        return $this->render(
            "tfktelemarkBundle:full:search.html.twig",
            array(
                "items" => $result['SearchResult'],
                'string' => $searchString,
                'count' => $result['SearchCount']
            )
        );
    }
}
