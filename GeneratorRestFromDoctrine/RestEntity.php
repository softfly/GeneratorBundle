<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

class RestEntity {

    private $singular_name;
    private $plurar_name;
    private $full_name;
    private $repo_name;
    private $get_method;
    private $map_type;
    private $columns = array();

    function getSingularName() {
        return $this->singular_name;
    }

    function getPlurarName() {
        return $this->plurar_name;
    }

    function getFullName() {
        return $this->full_name;
    }

    function getRepoName() {
        return $this->repo_name;
    }

    function getColumns() {
        return $this->columns;
    }

    function setSingularName($singular_name) {
        $this->singular_name = $singular_name;
    }

    function setPlurarName($plurar_name) {
        $this->plurar_name = $plurar_name;
    }

    function setFullName($full_name) {
        $this->full_name = $full_name;
    }

    function setRepoName($repo_name) {
        $this->repo_name = $repo_name;
    }

    function addColumn($column) {
        $this->columns[] = $column;
    }

    function setColumns($columns) {
        $this->columns = $columns;
    }

    function getGetMethod() {
        return $this->get_method;
    }

    function setGetMethod($get_method) {
        $this->get_method = $get_method;
    }
    
    function getMapType() {
        return $this->map_type;
    }

    function setMapType($map_type) {
        $this->map_type = $map_type;
    }
   
    function toArray() {
        $a = array(
            'singular_name' => $this->singular_name,
            'plurar_name' => $this->plurar_name,
            'full_name' => $this->full_name,
            'repo_name' => $this->repo_name,
            'get_method' => $this->get_method,
            'map_type' => $this->map_type,
            'columns' => array()
        );
        foreach ($this->columns as $column) {
            $a['columns'][] = $column->toArray();
        }
        return $a;
    }


}
