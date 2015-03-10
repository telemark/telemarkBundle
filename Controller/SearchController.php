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
     * Displays the search box for the page header
     *
     * @return Response HTML code of the page
     */
    public function searchBoxAction()
    {
        $response = new Response();

        /** @var SearchAdapter $searchAdapter */
        $searchAdapter = $this->get('netgen_search_and_filter.route_');
        /** @var Form $form */
        $form = $searchAdapter->getForm();

        return $this->render(
            'tfktelemarkBundle::page_header_searchbox.html.twig',
            array(
                'form_template' => $searchAdapter->getFormTemplate(),
                'form' => $form->createView()
            ),
            $response
        );
    }
}
