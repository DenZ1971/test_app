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
            ->addOption('levels', 'l', InputArgument::OPTIONAL, 'Number of levels to display', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('directory');
        $levels = (int)$input->getOption('levels');

        $info = $this->fileService->getTreeInfo($directory);

        $this->outputTreeInfo($info, $output, $levels);

        return Command::SUCCESS;
    }


    private function outputTreeInfo(array $info, OutputInterface $output, $maxLevels, $currentLevel = 0)
    {
        if ($currentLevel === 0) {
            $output->writeln("Name \t\t\t\t\t Dirs \t\t Files \t\t Links \t\t TotalSize");
        }

        if ($currentLevel <= $maxLevels) {
            $spaces = str_repeat('.', $currentLevel * 1);
            $output->writeln(sprintf(
                "%s %-22s \t\t %d \t\t %d \t\t %d \t\t %d",
                $spaces,
                $info['name'],
                $info['folders'],
                $info['files'],
                $info['links'],
                $info['totalSize']
            ));

            foreach ($info['children'] as $childInfo) {
                $this->outputTreeInfo($childInfo, $output, $maxLevels, $currentLevel + 1);
            }
        }
    }
}