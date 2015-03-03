<?php
namespace tfk\telemarkBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Location;

class SlideContainerController extends Controller
{
    public function slidesAction( Location $location )
     {
         $query = new Query();
         $query->criterion = new Criterion\LogicalAnd(
             array(
                 new Criterion\ParentLocationId( $location.id ),
                 new Criterion\ContentTypeIdentifier( 'slide' )
             )
         );
         $result = $this->getRepository()->getSearchService()->findContent($query);
         $slides = array();
         foreach($result->searchHits as $hit)
         {
                 $slides[] = $hit->valueObject;
         }
        return $this->render(
                 'telemarkBundle:line:slide_container.html.twig',
                 array('slides' => $slides)
         );
     }
}