<?php

namespace App\Command;

use App\BaseScraperBundle\Command\Base\BasicParseCommand;
use App\Entity\CrawlingQueue;
use App\Service\ReviewQueueParser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppTrackingReviewsUpdateCommand extends BasicParseCommand
{

    public function __construct(ReviewQueueParser $parser)
    {
        $this->parser = $parser;
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setName('app:tracking:reviews:update')
            ->setDescription('Parsing updated reviews for products.')
            ->addArgument('restartQueue', InputArgument::IS_ARRAY, 'If there is a given argument, the script will get a queue for failed products to restart.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output);
        
        // Set queue TYPE specific to this task
        $this->crawlingQueueType = CrawlingQueue::getTypeReviewsUpdate();
        
        $this->startTrackingsProcessing();

        foreach ($this->trackings as $tracking) {
            $this->setVariables($tracking);
            
            $this->startItemProcessing($this->tracking, 'Creating review update queue');

            $this->processTracking();

            $this->finishItemProcessing('Reviews update queue finished');
        }
        
        $this->checkAndSetParentQueueProcessing();

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
