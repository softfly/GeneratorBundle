<?php

namespace Softfly\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Softfly\GeneratorBundle\GeneratorRestFromDoctrine\GeneratorRestFromDoctrine;

class RestEntityGenerateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('softfly:generate:rest')
                ->setDescription('Generate REST based on Entity')
                ->addOption(
                        'output', 'o', InputOption::VALUE_REQUIRED, 'Output class entity'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $input->getOption('output');
        $generator = $this->getContainer()->get('softfly_generator.GeneratorRestFromDoctrine');
        $generator->execute();
    }

}
