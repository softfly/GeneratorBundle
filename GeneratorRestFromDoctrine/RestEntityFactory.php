<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

class RestEntityFactory {

    public function create(EntityClass $entityClass ) {
        $renderEntity = new RestEntity();
        $renderEntity->setSingularName(lcfirst($entityClass->getSingluarName()));
        $renderEntity->setPlurarName(lcfirst($entityClass->getPluralName()));
        $renderEntity->setFullName('\\' . $entityClass->getFullName());
        $repo = $this->getDoctrine()->getRepository($entityClass->getFullName());
        $renderEntity->setRepoName('\\' . get_class($repo));
        
        return $renderEntity;
    }

}
