<?php

 namespace calculator\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class CalculatorCommand extends Command
{
    
    protected static $defaultName = 'calculator';

    protected function configure()
    {
        
        $this
        ->addArgument('operation',InputArgument::REQUIRED)
        ->addArgument('argument1',InputArgument::REQUIRED)
        ->addArgument('argument2',InputArgument::REQUIRED)
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
         $arg1 = $input->getArgument('argument1');
         $arg2= $input->getArgument('argument2');
         $total= $input->getArgument('operation');
       
            if($total=='add' )
            {
               $add=$arg1+$arg2;
               
              $output->writeln("Addition".'='.$add);
            }
            else if($total=='sub' )
            {
               $sub=$arg1-$arg2;
               
              $output->writeln("Subtraction".'='.$sub);
            }
            else if($total=='mul')
            {
                $mul=$arg1*$arg2;
               
              $output->writeln("Multiplication".'='.$mul);
            }
            else if($total=='div' )
            {
                

                $div=$arg1/$arg2;
                $output->writeln("Division".'='.$div);
             
               
              
            }
    }
}