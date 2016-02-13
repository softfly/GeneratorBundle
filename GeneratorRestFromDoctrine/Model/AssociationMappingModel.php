<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model;

class AssociationMappingModel {

    private $singular_name;
    private $full_name;
    private $get_method;
    private $map_type;
    private $depth;
    private $columns = array();
    private $mapping = array();

    function getSingularName() {
        return $this->singular_name;
    }

    function getFullName() {
        return $this->full_name;
    }

    function getGetMethod() {
        return $this->get_method;
    }

    function getMapType() {
        return $this->map_type;
    }

    function getColumns() {
        return $this->columns;
    }

    function getMapping() {
        return $this->mapping;
    }

    function setSingularName($singular_name) {
        $this->singular_name = $singular_name;
    }

    function setFullName($full_name) {
        $this->full_name = $full_name;
    }

    function setGetMethod($get_method) {
        $this->get_method = $get_method;
    }

    function setMapType($map_type) {
        $this->map_type = $map_type;
    }

    function setColumns($columns) {
        $this->columns = $columns;
    }

    function setMapping($mapping) {
        $this->mapping = $mapping;
    }
    
    function getDepth() {
        return $this->depth;
    }

    function setDepth($depth) {
        $this->depth = $depth;
    }

    function toArray() {
        $a = array(
            'singular_name' => $this->singular_name,
            'full_name' => $this->full_name,
            'get_method' => $this->get_method,
            'map_type' => $this->map_type,
            'depth' => $this->depth,
        );
        $a['columns']=array();
        foreach ($this->columns as $column) {
            $a['columns'][] = $column->toArray();
        }
        $a['mapping']=array();
        foreach ($this->mapping as $mapping) {
            $a['mapping'][] = $mapping->toArray();
        }
        return $a;
    }

}
