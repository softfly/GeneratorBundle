Softfly Generator for Symfony
===========================

Install
-------------------------
composer require softfly/generator-bundle


Functionalities
-------------------------
1. Generate REST based on Doctrine:
..1. Method get all entities
....1. Generate mapping entitly to response object. 
....2. Generate recursive mappings for relation entity.
....3. Protection for "A circular reference"


Example
-------------------------
```php
namespace AppBundle\Controller\Rest\Properties;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;

class PropertyRest extends FOSRestController
{
    public function getPropertiesAction()
    {
        $data = array();
        /* @var $propertiesRepo \Doctrine\ORM\EntityRepository */
        $propertiesRepo = $this->getDoctrine()->getRepository('\\AppBundle\\Entity\\Properties\\Property');
        $properties = $propertiesRepo->findAll();
        /* @var $property \AppBundle\Entity\Properties\Property*/
        foreach ($properties as $property) {
            $row = array();
            $row['id'] = $property->getId();
            $row['name'] = $property->getName();
            $row['slug'] = $property->getSlug();
            $row['location'] = $property->getLocation();
            $row['short_description'] = $property->getShortDescription();
            $row['long_description'] = $property->getLongDescription();
            /* @var $price \AppBundle\Entity\Offers\Price*/
            foreach ($property->getPrices() as $price) {
                $row1 = array();
                $row1['id'] = $price->getId();
                $row1['price'] = $price->getPrice();
                $row['price'][] = $row1;
                /* @var $timeUnit \AppBundle\Entity\Offers\TimeUnit*/
                $timeUnit = $price->getTimeUnit();
                if ($timeUnit) {
                    $row2 = array();
                    $row2['id'] = $timeUnit->getId();
                    $row2['noun'] = $timeUnit->getNoun();
                    $row2['adverb'] = $timeUnit->getAdverb();
                    $row['timeUnit'] = $row2;
                }
            }
            $data[] = $row;
        }
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}
```


