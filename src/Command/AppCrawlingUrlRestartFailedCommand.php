<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 03.07.2018
 * Time: 14:54
 */

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppCrawlingUrlRestartFailedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlingUrlRestartFailedCommand extends BaseAppCrawlingUrlRestartFailedCommand
{

    protected function configure()
    {
        $this
            ->setName('app:crawlingUrl:restartFailed')
            ->setDescription('Creates a queue of urls for failed reviews and products parsing.')
            ->addOption('noTimeout', null, InputOption::VALUE_OPTIONAL, 'Restart URLs without waiting for timeout.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output);

        $this
            ->setOptions()
            ->findUrls()
            ->processUrls();
        
        $this->sendFailedAttemptsCountMail();

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

    protected function checkAndDeleteElement($url)
    {
        // Deleting reviews if they were created
        if ($url->isTypeReview()) {
            return $this->deleteReviewsForCrawlingUrl($url);
        }

        return true;
    }

}