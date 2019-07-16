<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 20.07.2018
 * Time: 11:30
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppStatsViewFailedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppStatsViewFailedCommand extends BaseAppStatsViewFailedCommand
{
    protected function configure()
    {
        $this
            ->setName('app:stats:viewFailed')
            ->setDescription("Shows information about the table 'tracking' and failed information about 'crawlingQueue' and 'crawlingUrl'")
            ->addOption('queueId', '-qi', InputOption::VALUE_OPTIONAL, 'Enter id of CrawlingQueue.')
            ->addOption('urlId', '-ui', InputOption::VALUE_OPTIONAL, 'Enter id of CrawlingUrl.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);
        
        $this
            ->setOptions()
            ->formDataTables()
            ->processTable(self::TRACKING_FAILED, true);

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