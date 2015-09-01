<?php
/**
 * This file is part of the DemoBundle package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributd with this source code.
 * @version //autogentag//
 */
namespace tfk\telemarkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishCoreBundle\Controller;

class MenuController extends Controller
{
    /**
     * @param mixed|null $currentLocationId
     * @return Response
     */
	  public function leftMenuAction( $currentLocationId )
    {
        /*if ( $currentLocationId !== null )
        {
            $location = $this->getLocationService()->loadLocation( $currentLocationId );
            if ( isset( $location->path[2] ) )
            {
                var_dump($location->path[2]);
                $secondLevelLocationId = $location->path[2];
            }
        }
        */

        $response = new Response;

        $menu = $this->getMenu( 'top' );
        $menu->setChildrenAttribute('class', 'nav side-menu');
        $parameters = array( 'menu' => $menu );

        /*if ( isset( $secondLevelLocationId ) && isset( $menu[$secondLevelLocationId] ) )
        {
            $parameters['submenu'] = $menu[$secondLevelLocationId];
        }*/

        return $this->render( 'tfktelemarkBundle::page_leftmenu.html.twig', $parameters, $response );
    }
	
    public function topMenuAction( $currentLocationId )
    {
        if ( $currentLocationId !== null )
        {
            $location = $this->getLocationService()->loadLocation( $currentLocationId );
            if ( isset( $location->path[2] ) )
            {
                $secondLevelLocationId = $location->path[2];
            }
        }

        $response = new Response;

        $menu = $this->getMenu( 'top' );
        $parameters = array( 'menu' => $menu );

        if ( isset( $secondLevelLocationId ) && isset( $menu[$secondLevelLocationId] ) )
        {
            $parameters['submenu'] = $menu[$secondLevelLocationId];
        }

        return $this->render( 'tfktelemarkBundle::page_topmenu.html.twig', $parameters, $response );
    }

    /**
     * @param string $identifier
     * @return \Knp\Menu\MenuItem
     */
    private function getMenu( $identifier )
    {
        return $this->container->get( "telemark.menu.$identifier" );
    }

    /**
     * @return \eZ\Publish\API\Repository\LocationService
     */
    private function getLocationService()
    {
        return $this->container->get( 'ezpublish.api.service.location' );
    }
}
