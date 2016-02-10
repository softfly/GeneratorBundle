<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

use Doctrine\ORM\EntityManager;

class EntityClass {
    /* @var $em \Doctrine\ORM\EntityManager */

    private $em;
    private $entity;
    /* @var $classMetadata \Doctrine\ORM\Mapping\ClassMetadata */
    private $classMetadata;

    function __construct($entityClass, EntityManager $em) {
        $this->entity = new $entityClass();

        $this->em = $em;
        $this->classMetadata = $em->getClassMetadata($entityClass);
    }

    function getEntity() {
        return $this->entity;
    }

    function getClassMetadata() {
        return $this->classMetadata;
    }
    
    function getFullName() {
        return $this->classMetadata->name;
    }

    function getSingluarName() {
        return strtolower(substr(strrchr($this->classMetadata->getName(), '\\'), 1));
    }

    function getPluralName() {
        return $this->getPluralPrase($this->getSingluarName());
    }

    private function getPluralPrase($phrase) {
        $plural = '';
        for ($i = 0; $i < strlen($phrase); $i++) {
            if ($i == strlen($phrase) - 1) {
                $plural.=($phrase[$i] == 'y') ? 'ies' : (($phrase[$i] == 's' || $phrase[$i] == 'x' || $phrase[$i] == 'z' || $phrase[$i] == 'ch' || $phrase[$i] == 'sh') ? $phrase[$i] . 'es' : $phrase[$i] . 's');
            } else {
                $plural.=$phrase[$i];
            }
        }
        return $plural;
    }

}
