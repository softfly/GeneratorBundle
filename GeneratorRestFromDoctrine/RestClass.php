<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

class RestClass {

    private $restDir;
    private $restClass;
    private $restPath;

    function __construct(EntityClass $entitlyClass) {
        $this->restClass = $this->getRestClassHelper($entitlyClass) . "Rest";
        $this->restDir = $this->getRestDirHelper($entitlyClass);
        $this->restPath = 'src\\' . $this->restDir . '\\' . $this->restClass . '.php';
    }

    public function parseCode() {
        $code = file_get_contents($this->restPath);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        try {
            $stmts = $parser->parse($code);
        } catch (Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
        return $stmts;
    }

    public function saveCode($stmts) {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $prettyPrinter = new PrettyPrinter\Standard;
        try {
            $code = $prettyPrinter->prettyPrintFile($stmts);
        } catch (Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
        if (!file_exists('src\\' . $this->restDir)) {
            mkdir('src\\' . $this->restDir, '755', true);
        }
        file_put_contents($this->restPath, $code);
    }

    private function getRestClassHelper(EntityClass $entitlyClass) {
        $class = $entitlyClass->getEntity();
        $a = explode('\\', get_class($class));
        return $a[sizeof($a) - 1];
    }

    private function getRestDirHelper(EntityClass $entitlyClass) {
        $namespace = $entitlyClass->getClassMetadata()->namespace;

        if (preg_match('/entity/i', $namespace)) {
            $restDir = str_ireplace('entity', 'Controller\Rest', $namespace);
        } else {
            $a = explode('\\', $namespace);
            $b = array_merge(array_slice($a, 0, 1), array('Controller', 'Rest'), array_slice($a, 2));
            $restDir = implode('\\', $b);
        }
        return $restDir;
    }

    function getRestDir() {
        return $this->restDir;
    }

    function getRestClass() {
        return $this->restClass;
    }

    function getRestPath() {
        return $this->restPath;
    }
    

}
