<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 03.07.2018
 * Time: 12:52
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppCrawlingQueueRestartFailedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlingQueueRestartFailedCommand extends BaseAppCrawlingQueueRestartFailedCommand
{
    protected function configure()
    {
        $this
            ->setName('app:crawlingQueue:restartFailed')
            ->setDescription('Creates a queue of urls for failed reviews and products parsing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output);

        // Find failed queues and queues scheduled to delete
        $this->findFailedQueues()
            ->processFailedQueues($output);

        // Second - we need to cleanup queues scheduled to delete
        $this->findDeletedQueues()
            ->processDeletedQueues();
        
        $this->finishProcessing();
    }

    protected function getCommandName()
    {
        $command = false;

        if ($this->parentQueue->isStatusFailedProductsParse()) {
            $command = 'app:tracking:products:parse';
        }
        if ($this->parentQueue->isStatusFailedReviewsCreate()) {
            $command = 'app:tracking:reviews:create';
        }
        if ($this->parentQueue->isStatusFailedReviewsUpdate()) {
            $command = 'app:tracking:reviews:update';
        }

        return $command;
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