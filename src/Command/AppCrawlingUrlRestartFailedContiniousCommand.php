<?php

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppCrawlingUrlRestartFailedContiniousCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlingUrlRestartFailedContiniousCommand extends BaseAppCrawlingUrlRestartFailedContiniousCommand
{

    protected function configure()
    {
        $this
            ->setName('app:crawlingUrl:restartFailedContinious')
            ->setDescription('Manually restarted continiously failed URLS.')
            ->addOption('urlId', null, InputOption::VALUE_OPTIONAL, 'Specify id of URL to restart.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output);
        
        $this
            ->setOptions()
            ->findUrls()
            ->processUrls();

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