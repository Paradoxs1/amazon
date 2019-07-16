<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 19.07.2018
 * Time: 11:18
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppStatsViewEntityCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppStatsViewEntityCommand extends BaseAppStatsViewEntityCommand
{
    
    protected function configure()
    {
        $this
            ->setName('app:stats:viewEntity')
            ->addOption('tracking', '-t', InputOption::VALUE_OPTIONAL, 'Enter tracking id. Specify 0 to get ALL.')
            ->addOption('asin', '-a', InputOption::VALUE_OPTIONAL, 'Enter tracking ASIN.')
            ->addOption('queue', '-queue', InputOption::VALUE_OPTIONAL, 'Enter id of CrawlingQueue.')
            ->addOption('url', '-url', InputOption::VALUE_OPTIONAL, 'Enter id of CrawlingUrl.')
            ->setDescription("Displays table data 'tracking', 'queue' and 'url'. Use as parameter id or asin.");
        
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);

        $this
            ->setOptions()
            ->formDataTables();
        
        foreach ($this->dataTables as $table) {
            $this->processEntityTable($table);
        }

        if ($this->sendEmail) {
            $this->sendStatsEmail();
        }

        $this->finishProcessing();
    }

    protected function deleteObjectForCrawlingUrl($crawlingUrl)
    {
        $this->deleteReviewsForCrawlingUrl($crawlingUrl);
    }

    protected function deleteReviewsForCrawlingUrl($crawlingUrl)
    {
        $reviewsDeleted = 0;
        $reviews = $crawlingUrl->getReviews();

        if (count($reviews) > 0) {
            foreach ($reviews as $review) {
                $this->em->remove($review);
                $reviewsDeleted++;
            }
            $this->em->flush();
        }

        $this->comment("Deleted Reviews: {$reviewsDeleted}.");

        return true;
    }

}