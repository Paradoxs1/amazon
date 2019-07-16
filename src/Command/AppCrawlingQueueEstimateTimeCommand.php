<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 18.07.2018
 * Time: 15:10
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppCrawlingQueueEstimateTimeCommand;
use App\Entity\CrawlingQueue;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlingQueueEstimateTimeCommand extends BaseAppCrawlingQueueEstimateTimeCommand
{

    protected function configure()
    {
        $this
            ->setName('app:crawlingQueue:estimateTime')
            ->setDescription('Get processing speed and exceptions from log files.')
            ->addOption('type', '-tp', InputOption::VALUE_OPTIONAL, 'Enter type of CrawlingQueue.')
            ->addOption('id', '-id', InputOption::VALUE_OPTIONAL, 'Enter id of CrawlingQueue.');
        
        $this->crawlingQueueTypes = CrawlingQueue::getTypes();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);

        $this->setOptions($input);
        
        $this->setProcessingSpeed($output);
        $this->setProcessingCrawlingQueues();

        if ($this->type == CrawlingQueue::getTypeProductsParse()) {
            $this->calculateProductParse();
        }
        if ($this->type == CrawlingQueue::getTypeReviewsCreate()) {
                $this->calculateReviewsCreate();
        }
        if ($this->type == CrawlingQueue::getTypeReviewsUpdate()) {
            $this->calculateReviewsUpdate();
        }

        $this->finishProcessing();
        
        return $this->time;
    }

    protected function calculateProductParse()
    {
        $urlsCount = 0;

        foreach ($this->crawlingQueues as $item) {
            $urlsCount += count($item->getCrawlingUrls());
        }

        $timeParsing = $urlsCount * (60 / $this->processingSpeed);
        $this->generateTimeParsing($timeParsing);
    }

    protected function calculateReviewsCreate()
    {
        foreach ($this->crawlingQueues as $item) {
            $this->totalUrlsCount += count($item->getCrawlingUrls());
            foreach ($item->getCrawlingUrls() as $url) {
                if ($url->isTypeReviewQueue() && $url->isStatusScraped()) {
                    $this->typeUrlsCount++;
                }
            }
        }

        $this->calculateUrlCount();
    }

    protected function calculateReviewsUpdate()
    {
        // coefficient for posibility that parser will need to go deeper through pages
        $coeff = 1.5;

        $timeParsing = $coeff * count($this->crawlingQueues) * (60 / $this->processingSpeed);
        $this->generateTimeParsing($timeParsing);
    }
}