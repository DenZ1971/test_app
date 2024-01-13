<?php

namespace App\Console;

use App\Service\FileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateTreeCommand extends Command
{
    private $fileService;

    protected static $defaultName = 'app:tree';

    public function __construct(FileService $fileService)
    {
        parent::__construct();

        $this->fileService = $fileService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create tree command')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory path')
            ->addOption('levels', 'l', InputArgument::OPTIONAL, 'Number of levels to display', 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('directory');
        $levels = (int)$input->getOption('levels');

        $info = $this->fileService->getTreeInfo($directory, $levels);

        $this->outputTreeInfo($info, $output);

        return Command::SUCCESS;
    }

    private function outputTreeInfo(array $info, OutputInterface $output, $indent = 0)
    {
        if ($indent === 0) {
            $output->writeln("Name \t  Dirs \t  Files \t  Links \t TotalSize");
        }

        $spaces = str_repeat('.', $indent);
        $output->writeln("{$spaces}{$info['name']} \t {$info['folders']} \t {$info['files']} \t {$info['links']} \t {$info['totalSize']}");

        foreach ($info as $childInfo) {
            if (is_array($childInfo) && isset($childInfo['name'])) {
                $this->outputTreeInfo($childInfo, $output, $indent + 1);
            }
        }
    }
}