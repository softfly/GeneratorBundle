<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model;

class ColumnModel {

    private $singular_name;
    private $get_method;

    function getSingularName() {
        return $this->singular_name;
    }

    function getGetMethod() {
        return $this->get_method;
    }

    function setSingularName($singular_name) {
        $this->singular_name = $singular_name;
    }

    function setGetMethod($get_method) {
        $this->get_method = $get_method;
    }

    function toArray() {
        return array(
            'singular_name' => $this->singular_name,
            'get_method' => $this->get_method,
        );
    }

}
