<?php

/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 26.06.2018
 * Time: 17:15
 */

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class AppLogRotateCommand extends AppLogAnalyzeCommand
{

    protected function configure()
    {
        $this
            ->setName('app:log:rotate')
            ->setDescription('Weekly move logs to archive folder.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);
        $this->fileSystem = new Filesystem();

        $currentDate = new \DateTime();
        $dateFormat = $currentDate->format('Y-m-d');
        
        $this->setLogDirectories($dateFormat);
        
        $counter = 0;
        
        foreach ($this->allLogFiles as $file) {
            $baseFilePath = $this->getFile($file, true);
            if ($baseFilePath !== false) {
                $archiveFilePath = $this->getFullFilePath($file);
                
                $this->fileSystem->rename($baseFilePath, $archiveFilePath);
                $counter++;
            }
        }
        
        $this->comment("Moved {$counter} files to {$dateFormat} archive.");

        $this->finishProcessing();
    }

}
