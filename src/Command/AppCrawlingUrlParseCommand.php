<?php

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppCrawlingUrlParseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCrawlingUrlParseCommand extends BaseAppCrawlingUrlParseCommand
{

    protected function configure()
    {
        $this
            ->setName('app:crawlingUrl:parse')
            ->setDescription('Parse all kind of links in CrawlingUrl');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output);
        $parsed = 0;

        $crawlingUrls = $this->findCrawlingUrls();

        foreach ($crawlingUrls as $crawlingUrl) {
            $this->setVariables($crawlingUrl);
            
            $this->startItemProcessing($this->crawlingUrl, 'Parsing');
            
            $this->processCrawlingUrl();

            $this->finishItemProcessing('Finishing url');

            $parsed++;
            
            if ($this->isTimeLimitReached()) {
                break;
            }
        }
        
        $this->comment("Exit: {$parsed} urls were parsed.");
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