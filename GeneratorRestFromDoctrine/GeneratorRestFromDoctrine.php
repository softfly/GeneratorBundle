<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\BuilderFactory;
use Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model\EntityModel;
use Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model\ColumnModel;
use Softfly\GeneratorBundle\GeneratorRestFromDoctrine\Model\AssociationMappingModel;

class GeneratorRestFromDoctrine {

    /**
     * @var \Softfly\GeneratorBundle\GeneratorRestFromDoctrine\EntityClass $entityClass  
     */
    private $entityClass;

    /**
     * @var \Softfly\GeneratorBundle\GeneratorRestFromDoctrine\RestClass $restClass  
     */
    private $restClass;

    /**
     * @var \PhpParser\BuilderFactory $factory  
     */
    private $factory;

    /**
     * @var \Twig_Environment $twig  
     */
    private $twig;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine  
     */
    private $doctrine;

    /**
     * @var \Doctrine\ORM\EntityManager $em  
     */
    private $em;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container  
     */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->twig = $this->getContainer()->get('twig');
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->em = $this->getDoctrine()->getManager();
        $this->factory = new BuilderFactory;
    }

    public function execute($outEntityClass = 'AppBundle\Entity\Properties\Property') {
        $this->setEntityClass(new EntityClass($outEntityClass, $this->getEm()));
        $this->setRestClass(new RestClass($this->getEntityClass()));

        //Reengineering
        //$this->stmts = $this->restClass->parseCode();
        //$this->stmts = array();
        //var_dump($this->stmts);
        $this->getRestClass()->saveCode(array($this->getStmt()));
    }

    private function getStmt() {
        $uses = array(
            'FOS\RestBundle\Controller\Annotations\RouteResource',
            'FOS\RestBundle\Controller\FOSRestController'
        );
        //Namespace
        $builder = $this->getFactory()
                ->namespace($this->getRestClass()->getRestDir());
        //Use
        foreach ($uses as $use) {
            $builder = $builder->addStmt($this->getFactory()->use($use));
        }
        //Class
        $builder = $builder->addStmt($this->getClass());
        return $builder->getNode();
    }

    private function getClass() {
        return $this->getFactory()
                        ->class($this->getRestClass()->getRestClass())
                        ->extend('FOSRestController')
                        ->addStmt($this->getMethodGetAll());
    }

    private function getMethodGetAll() {
        $entityModel = new EntityModel();
        $entityWrapper = $this->getEntityClass();
        $entityModel->setSingularName(lcfirst($entityWrapper->getSingluarName()));
        $entityModel->setPlurarName(lcfirst($entityWrapper->getPluralName()));
        $entityModel->setFullName('\\' . $entityWrapper->getFullName());
        $repo = $this->getDoctrine()->getRepository($entityWrapper->getFullName());
        $entityModel->setRepoName('\\' . get_class($repo));
        $entityModel->setColumns($this->getColumns($entityWrapper));
        $entityModel->setMapping($this->getAssociationMappings($entityWrapper));

        $code = '<?php ' . PHP_EOL;
        $code .= $this->getTwig()->render('SoftflyGeneratorBundle:Rest:findAll.html.twig', $entityModel->toArray());
        $stmts = $this->parseCode($code);

        return $this->getFactory()
                        ->method('get' . ucfirst($this->entityClass->getPluralName()) . 'Action')
                        ->makePublic()
                        ->addStmts($stmts);
    }

    private function getColumns(EntityClass $entityWrapper) {
        $columns = array();
        foreach ($entityWrapper->getClassMetadata()->getColumnNames() as $column) {
                $columnModel = new ColumnModel();
                $getMethod = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $column)));
                if (method_exists($entityWrapper->getEntity(), $getMethod)) {
                    $columnModel->setSingularName($column);
                    $columnModel->setGetMethod($getMethod);
                }
                $columns[] = $columnModel;
        }
        return $columns;
    }
    
    private $depth=0;
    private $max_depth = 10;
    
    private function getAssociationMappings(EntityClass $entityWrapper, $ignore=null) {
        if ($this->depth >= $this->max_depth) {
            return array();
        } else {
            $this->depth++;
            $mappings = array();
            foreach ($entityWrapper->getClassMetadata()->getAssociationMappings() as $column) {
                if ($ignore != $column['fieldName']) {
                    $mappingModel = new AssociationMappingModel();
                    $entityWrapper2 = new EntityClass($column['targetEntity'], $this->getEm());
                    $mappingModel->setSingularName(lcfirst($entityWrapper2->getSingluarName()));
                    $mappingModel->setFullName('\\' . $entityWrapper2->getFullName());
                    $mappingModel->setGetMethod('get' . ucfirst($this->camelize($column['fieldName'])));
                    $mappingModel->setMapType($column['type']);
                    $mappingModel->setDepth($this->depth);
                    $mappingModel->setColumns($this->getColumns($entityWrapper2, $column['mappedBy'])); 
                    $mappingModel->setMapping($this->getAssociationMappings($entityWrapper2, $column['mappedBy']));
                    $mappings[] = $mappingModel;
                }
            }
            $this->depth--;
            return $mappings;
        }
    }

    private function parseCode($code) {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        try {
            $stmts = $parser->parse($code);
            return $stmts;
        } catch (Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
    }

    private function uncamelize($camel, $splitter = "_") {
        $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel));
        return strtolower($camel);
    }

    private function camelize($str) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    private function getPrefixRoute() {
        $namespace = get_class($this->getEntityClass()->getEntity());
        $namespace = str_ireplace('\entity', '', $namespace);
        $namespace = explode('\\', $namespace);
        array_shift($namespace);
        array_pop($namespace);
        $namespace = strtolower(implode('/', $namespace));
        return $namespace;
    }

    function getTwig() {
        return $this->twig;
    }

    function getDoctrine() {
        return $this->doctrine;
    }

    function getEm() {
        return $this->em;
    }

    function getContainer() {
        return $this->container;
    }

    function getEntityClass() {
        return $this->entityClass;
    }

    function getRestClass() {
        return $this->restClass;
    }

    function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;
    }

    function setRestClass($restClass) {
        $this->restClass = $restClass;
    }

    function getFactory() {
        return $this->factory;
    }

}
