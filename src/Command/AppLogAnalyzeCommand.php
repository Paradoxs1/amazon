<?php

namespace App\Command;

use App\BaseScraperBundle\Command\BaseAppLogAnalyzeCommand;
use App\BaseScraperBundle\Service\BaseConfig;
use App\BaseScraperBundle\Service\BaseTwig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class AppLogAnalyzeCommand extends BaseAppLogAnalyzeCommand
{

    const
        LOG_TRACKING_REVIEWS_CREATE = 'app_tracking_reviews_create',
        LOG_TRACKING_PRODUCTS_PARSE = 'app_tracking_products_parse',
        LOG_TRACKING_REVIEWS_UPDATE = 'app_tracking_reviews_update';

    public function __construct(BaseConfig $config = null, \Swift_Mailer $mailer = null, BaseTwig $twig = null)
    {
        parent::__construct($config, $mailer, $twig);
        $this->setAllLogFiles();
    }

    protected function configure()
    {
        $this
            ->setName('app:log:analyze')
            ->setDescription('Get processing speed and exceptions from log files.')
            ->addOption('speed', null, InputOption::VALUE_OPTIONAL, 'Display only speed')
            ->addOption('archive', null, InputOption::VALUE_OPTIONAL, 'Enter archive directory postfix')
            ->addOption('entity', null, InputOption::VALUE_OPTIONAL, "You can specify entity name, or entity id, or both as string: 'CrawlingUrl', '153' or 'CrawlingUrl 153'");
        
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommandProcessing($input, $output, true);
        
        $this
            ->setOptions()
            ->setLogDirectories();
        
        if (!$this->forEntity) {
            $this->processSpeed();
            if ($this->onlySpeed) {
                return $this->processingSpeed;
            }
        }
        
        $this->processExceptions();
        
        if ($this->sendEmail) {
            $this->sendLogsEmail();
        }

        $this->finishProcessing();
    }

    protected function setAllLogFiles()
    {
        $this->allLogFiles = [
            self::LOG_CRAWLINGURL_PARSE,
            self::LOG_TRACKING_REVIEWS_CREATE,
            self::LOG_TRACKING_PRODUCTS_PARSE,
            self::LOG_TRACKING_REVIEWS_UPDATE,
            self::LOG_TRACKING_RESTART_FAILED,
            self::LOG_CRAWLINGURL_RESTART_FAILED,
        ];

        return $this;
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
