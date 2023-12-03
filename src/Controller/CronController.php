<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/process', name: 'process')]
    public function process(KernelInterface $kernel): Response
    {
        //runs every 10 minutes, and runs whole 10 minutes
        $time = 10 * 3 - 5;

        //order of receivers is important; higher they are sooner they are processed
        $receivers = [
            'import_cars',
            'import_car',
        ];
        $this->run($kernel, $receivers, $time);
        return new Response();
    }
    /**
     * @throws Exception
     */
    private function run(KernelInterface $kernel, array $receivers, int $time): void
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arguments = [
            'receivers' => $receivers,
            '--time-limit' => $time - 10, //worker will have additional 10 seconds to finish till set_time_limit kills the script
            '--no-reset' => '--no-reset',
        ];

        //for developing purpose, to see "dump" this code can be used..
        //$command = $application->find('messenger:consume');
        //$commandTester = new CommandTester($command);
        //$commandTester->execute($arguments);
        //order of receivers is important; higher they are sooner they are processed

        // You can use NullOutput() if you don't need the output
        $input = new ArrayInput(array_merge(['command' => 'messenger:consume'], $arguments));
        $output = new NullOutput();
        $application->run($input, $output);
    }
}