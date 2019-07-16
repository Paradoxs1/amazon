<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 12.07.2018
 * Time: 10:57
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppStatsViewCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppStatsViewCommand extends BaseAppStatsViewCommand
{

    protected function configure()
    {
        $this
            ->setName('app:stats:view')
            ->setDescription("Shows information about the table 'crawling_url', 'crawling_queue', 'tracking' and 'entity'");
        
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);

        $this
            ->setOptions()
            ->formDataTables();

        $this->tablesEntities = [
            self::TRACKING,
            self::REVIEW,
            self::PRODUCT
        ];
        
        foreach ($this->dataTables as $tableName) {
            $this->processTable($tableName);
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
