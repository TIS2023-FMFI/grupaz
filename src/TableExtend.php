<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Driver\Connection;

class TableExtend extends Command
{
    /*private $data;

    public function __construct(Connection  $data)
    {
        parent::__construct();

        $this->data = $data;
    }*/

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['ISBN', 'Title', 'Author']);
        /*foreach ($this->data as $row) {
            $table->addRow($row);
        }*/
        $table->render();

        return Command::SUCCESS;
    }
}