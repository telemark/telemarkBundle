<?php
/**
 * This file is part of the DemoBundle package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version 2014.11.1
 */
namespace tfk\telemarkBundle\Menu;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\Core\Helper\TranslationHelper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * A simple eZ Publish menu provider.
 *
 * Generates a two level menu, starting from the configured root node.
 * Locations below the root node and until a relative depth of 2 are included.
 * Only visible locations with a ContentType included in `MenuContentSettings.TopIdentifierList` in legacy's `menu.ini`
 * are included.
 */
class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var TranslationHelper
     */
    private $translationHelper;

    private $container;

    public function __construct (
        FactoryInterface $factory,
        SearchService $searchService,
        RouterInterface $router,
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        TranslationHelper $translationHelper,
        Container $container
    )
    {
        $this->factory = $factory;
        $this->searchService = $searchService;
        $this->router = $router;
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->translationHelper = $translationHelper;
        $this->container = $container;
    }

    public function createTopMenu( Request $request )
    {
        $menu = $this->factory->createItem( 'root' );
        $locationId = $request->attributes->get('currentLocationId');

        $loc = $this->locationService->loadLocation( $locationId );
        $mainLoc = $this->locationService->loadLocation( $request->attributes->get('locationId') );

        if ($mainLoc->depth >= 4)
            if ($loc->path[2] == 111 && $mainLoc->depth == 4 )
                $mainLocId = $mainLoc->id;
            else
                $mainLocId = $mainLoc->parentLocationId;
        else 
            $mainLocId = $mainLoc->id;

        $this->addLocationsToMenu(
            $menu,
            $this->getMenuItems($locationId),
            $mainLocId,
            $locationId
        );

        return $menu;
    }

    /**
     * Adds locations from $searchHit to $menu
     *
     * @param ItemInterface $menu
     * @param SearchHit[] $searchHits
     * @return void
     */
    private function addLocationsToMenu( ItemInterface $menu, array $searchHits, $mainLocId, $currentLocId )
    {
        foreach ( $searchHits as $searchHit )
        {
            /** @var Location $location */
            $location = $searchHit->valueObject;
            $menuItem = isset( $menu[$location->parentLocationId] ) ? $menu[$location->parentLocationId] : $menu;
            $menuItem->addChild(
                $location->id,
                array(
                    'label' => $this->translationHelper->getTranslatedContentNameByContentInfo( $location->contentInfo ),
                    'uri' => $this->router->generate( $location ),
                    'attributes' => array( 'class' => 'side-menu__item', 'id' => 'nav-location-' . $location->id )
                )
            );
            $menuItem->setChildrenAttribute( 'class', 'nav' );

            //add subitems
            if ($location->id == $mainLocId || ($location->parentLocationId == $mainLocId && $location->parentLocationId != $currentLocId)) {
                $subitems = $this->getMenuItems($location->id);
                foreach ($subitems as $subitem) {
                    $sublocation = $subitem->valueObject;
                    $menuItem[$location->id]->addChild(
                        $sublocation->id,
                        array(
                            'label' => $this->translationHelper->getTranslatedContentNameByContentInfo( $sublocation->contentInfo ),
                            'uri' => $this->router->generate( $sublocation ),
                            'attributes' => array( 'class' => 'side-menu__item', 'id' => 'nav-location-' . $sublocation->id )
                        )
                    );
                }
            }
        }
    }

    /**
     * Queries the repository for menu items, as locations filtered on the list in TopIdentifierList in menu.ini
     * @param int|string $rootLocationId Root location for menu items. Only two levels below this one are searched
     * @return SearchHit[]
     */
    private function getMenuItems( $rootLocationId )
    {
        $rootLocation = $this->locationService->loadLocation( $rootLocationId );

        $query = new LocationQuery();

        $query->query = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('folder', 'event_calendar', 'survey', 'frontpage') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\ParentLocationId( $rootLocationId ),
                new Criterion\LanguageCode( $this->configResolver->getParameter( 'languages' ) )
            )
        );
        $query->sortClauses = array( 
            new Query\SortClause\Location\Priority(Query::SORT_DESC),
        ); 

        return $this->searchService->findLocations( $query )->searchHits;
    }

    private function getTopMenuContentTypeIdentifierList()
    {
        return $this->configResolver->getParameter( 'MenuContentSettings.TopIdentifierList', 'menu' );
    }
}
