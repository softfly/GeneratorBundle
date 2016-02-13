<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model;

class EntityModel {

    private $singular_name;
    private $plurar_name;
    private $full_name;
    private $repo_name;
    private $columns = array();
    private $mapping = array();

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

    function getMapping() {
        return $this->mapping;
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

    function setColumns($columns) {
        $this->columns = $columns;
    }

    function setMapping($mapping) {
        $this->mapping = $mapping;
    }

    function addColumn($columns) {
        $this->columns[] = $columns;
    }

    function addMapping($mapping) {
        $this->mapping[] = $mapping;
    }

    function toArray() {
        $a = array(
            'singular_name' => $this->singular_name,
            'plurar_name' => $this->plurar_name,
            'full_name' => $this->full_name,
            'repo_name' => $this->repo_name,
        );
        $a['columns'] = array();
        foreach ($this->columns as $column) {
            $a['columns'][] = $column->toArray();
        }
        $a['mapping'] = array();
        foreach ($this->mapping as $mapping) {
            $a['mapping'][] = $mapping->toArray();
        }
        return $a;
    }

}
