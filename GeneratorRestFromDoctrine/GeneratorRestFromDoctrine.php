<?php

namespace Softfly\GeneratorBundle\GeneratorRestFromDoctrine;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\BuilderFactory;

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

    public function execute($outEntityClass = 'AppBundle\Entity\Offers\Property') {
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
            'Symfony\Bundle\FrameworkBundle\Controller\Controller',
            'Symfony\Component\HttpFoundation\JsonResponse',
            'Sensio\Bundle\FrameworkExtraBundle\Configuration\Route',
            'Sensio\Bundle\FrameworkExtraBundle\Configuration\Method'
        );
        //Namespace
        $builder = $this->getFactory()
                ->namespace($this->getRestClass()->getFullClassName());
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
                        ->extend('Controller')
                        ->setDocComment('/**
                            * @Route("/rest")
                            */')
                        ->addStmt($this->getMethodGetAll());
    }

    private function getMethodGetAll() {
        $context = array();
        $context['singular_name'] = $this->getEntityClass()->getSingluarName();
        $context['plurar_name'] = $plurar_name = $this->getEntityClass()->getPluralName();
        $context['full_name'] = $this->getEntityClass()->getFullName();

        //Get entity columns
        $columns = array();
        foreach ($this->getEntityClass()->getClassMetadata()->getColumnNames() as $column) {
            $row = array();
            $a = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $column)));
            if (method_exists($this->getEntityClass()->getEntity(), $a)) {
                $row['name'] = $column;
                $row['getMethod'] = $a;
            }
            $columns[$column] = $row;
        }
        $context['columns'] = $columns;
        $code = $this->getTwig()->render('SoftflyGeneratorBundle:Rest:findAll.html.twig', $context);
        $stmts = $this->parseCode($code);

        $namespace = $this->getPrefixRoute();
        
        return $this->getFactory()
                        ->method('get' . ucfirst($this->entityClass->getPluralName()))
                        ->makePublic()
                        ->setDocComment("/**
                            * @Route(\"/$namespace/$plurar_name.json\", name=\"$namespace/$plurar_name\")
                            * @Method({\"GET\"})
                            */")
                        ->addStmts($stmts);
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

    private function getPrefixRoute() {
        $namespace = get_class($this->getEntityClass()->getEntity());
        $namespace = str_ireplace('\entity', '', $namespace);
        $namespace = explode('\\', $namespace);
        array_shift($namespace);
        array_pop($namespace);
        $namespace = strtolower(implode('/', $namespace));
        return 'rest/'.$namespace;
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
