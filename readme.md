1. Branch release in the GIT. 
2. parser.sql - dump of the database with the goods in the tracking for parsing.
3. When installing the project with a git, run the command.
```sh
    composer install
```

4. Command to view application stats
```sh
General stats:
    php bin/console app:stats:view

Stats for tracking by id:
    php bin/console app:stats:view --tracking_id 12
used ID equal to 0 to see all trackings stats
    
Stats for tracking by ASIN:
    php bin/console app:stats:view --tracking_id B07B7WRBST
```
6. Command for parsing all kind of crawling URLs in queue
```sh
    php bin/console app:crawlingUrl:parse
```

7. Commands for parsing trackings:
A: creating Queue for scraping initial reviews
```sh
    php bin/console app:tracking:reviews:create
```

B: creating Queue for weekly scraping products update
```sh
    php bin/console app:tracking:products:parse
```

C: creating Queue for weekly scraping reviews update
```sh
    php bin/console app:tracking:reviews:update
```   

8. Commands for restart corresponding failed entities
```sh
    php bin/console app:crawlingUrl:restartFailed
    php bin/console app:crawlingQueue:restartFailed

    Running manually:
    php bin/console app:crawlingUrl:restartFailedContinious
```

7. Tracking processing logic:
A. Creating initial reviews
- Scripts pick up CREATED trackings (tracking.status == STATUS_CREATED) and create parent CrawlingQueue.
- Tracking is set to PROCESSING (tracking.status = STATUS_PROCESSING).
- For every tracking Child CrawlingQueue is created, then for every queue CrawlingUrls are created, child CrawlingQueue is set to PROCESSING (parentCrawlingQueue.status = STATUS_QUEUE_PROCESSING).
- CrawlingUrls are created with type TYPE_URL_REVIEW_QUEUE. When this URL is scraped, we will know quantity of pages that contains review.
- We create CrawlingUrls with type TYPE_REVIEW for all pages.
- CrawlingUrls are parsing. If failed - automatic restart.
- If ALL CrawlingUrls are parsed, child CrawlingQueue set to SCRAPED (childCrawlingQueue.status = STATUS_QUEUE_SCRAPED).
- If ALL child CrawlingQueues are parsed, parent CrawlingQueue set to SCRAPED (parentCrawlingQueue.status = STATUS_QUEUE_SCRAPED).
- After parent CrawlingQueue become scraped, all belonging trackings become SCRAPED (tracking.status = STATUS_REVIEWS_CREATED).

B. Parse products information
- Scripts pick up SCRAPED trackings (tracking.status == STATUS_REVIEWS_CREATED) and create parent CrawlingQueue.
- URLs for PARSE PRODUCT are created, and stored in child CrawlingQueue. Parent CrawlingQueue is set to processing (STATUS_QUEUE_PROCESSING).
- For parent (tracking.asin == tracking.parent) tracking we create 12 links, for child 24.
- URL also can be parent, it has bigger priority, and creates product itself, then setting ID of created product to child links.
- Child links add other information for product, filling all the fields.
- Instead of parsing, info for child link can be copied, if inside the same parent Queue (during current products parse) we already parsed the product with same parent ASIN. This valid only for "parent" information fields for product.
- After all we set SCRAPED statuses to CrawlingUrl, CrawlingQueue and parent CrawlingQueue.

C. Update reviews information
- Scripts pick up SCRAPED trackings (tracking.status == STATUS_REVIEWS_CREATED) and create parent CrawlingQueue.
- For every tracking Child CrawlingQueue is created, then for every queue CrawlingUrls are created, child CrawlingQueue is set to PROCESSING (parentCrawlingQueue.status = STATUS_QUEUE_PROCESSING).
- CrawlingUrls are created with type TYPE_REVIEW_UPDATE_QUEUE. When this URL is scraped, we will know quantity of pages that contains review.
- We create CrawlingUrls with type TYPE_REVIEW_UPDATE for all pages.
- CrawlingUrls are parsing. For update process we check if review is already exist in database.
- If review exist, we stop parsing and set all other CrawlingUrls to SKIPPED (crawlingUrl.status = STATUS_URL_SKIPPED)
- We sum SCRAPED and SKIPPED URLs and compare with total amount. If equal - child CrawlingQueue set to SCRAPED (childCrawlingQueue.status = STATUS_QUEUE_SCRAPED).
- If ALL child CrawlingQueues are scraped, parent CrawlingQueue set to SCRAPED (parentCrawlingQueue.status = STATUS_QUEUE_SCRAPED).

8. Statuses description

Tracking statuses:
    0 : STATUS_TRACKING_CREATED : tracking is just added to table (default value). without it processing will not start. This status is used for INITIAL CREATE of REVIEWS
    3 : STATUS_TRACKING_PROCESSING : CrawlingQueue were created, and process is started
    5 : STATUS_TRACKING_REVIEWS_CREATED : tracking is scraped, and reviews ready for download. Also this status is used for PARSE PRODUCTS information and UPDATING REVIEWS. Once it's set, it will never change

Tracking types:
    -1 : TYPE_TRACKING_SHUTDOWN : tracking is shut down, and will not processed anymore
    0 : TYPE_TRACKING_GENERAL : general tracking type

Crawling QUEUE statuses:
    -1 : STATUS_QUEUE_DELETED : Queue is scheduled to delete due to error and will be automatically removed
    0 : STATUS_QUEUE_CREATED : queue is created
    3 : STATUS_QUEUE_PROCESSING : queue is started processing
    5 : STATUS_QUEUE_SCRAPED : queue is scraped
    13 : STATUS_QUEUE_FAILED_CONTINIOUS : queue was failing continiously
    15 : STATUS_QUEUE_FAILED_REVIEWS_CREATE : creating of Queue for CREATE REVIEWS command is failed, it will be automatically restarted
    16 : STATUS_QUEUE_FAILED_PRODUCTS_PARSE : creating of Queue for PARSE PRODUCTS command is failed, it will be automatically restarted
    17 : STATUS_QUEUE_FAILED_REVIEWS_UPDATE : creating of URLs for UPDATE REVIEWS command is failed, it will be automatically restarted

Crawling QUEUE types:
    0 : TYPE_QUEUE_GENERAL : general Queue type
    1 : TYPE_QUEUE_PARENT : Queue that contain other queues as children for all types of processing
    2 : TYPE_QUEUE_PRODUCTS_PARSE : queue for products parse process
    3 : TYPE_QUEUE_REVIEWS_CREATE : queue for reviews create process
    4 : TYPE_QUEUE_REVIEWS_UPDATE : queue for reviews update process

Crawling URL statuses:
    -1 : STATUS_URL_DELETED : URL is scheduled to delete
    0 : STATUS_URL_CREATED : URL is created
    3 : STATUS_URL_PROCESSING : URL is processing
    5 : STATUS_URL_SCRAPED : URL is successfully scraped
    10 : STATUS_URL_SKIPPED : URL is skipped and value was copied from scraped product with same parent
    13 : STATUS_URL_FAILED_CONTINIOUS : url  was failing continiously
    15 : STATUS_URL_FAILED : scraping of URL is failed with any reason

Crawling URL types:
    15 : TYPE_URL_REVIEW_QUEUE : initial review create - link to first page, determine pages count
    13 : TYPE_URL_REVIEW : initial review create - links to pages for parsing reviews itself
    10 : TYPE_URL_PRODUCT_QUEUE : product parse - link to first url for creating the product
    8 : TYPE_URL_PRODUCT : product parse - links to child URL, for parsing all other information
    5 : TYPE_URL_REVIEW_UPDATE_QUEUE : updating reviews - link to first page, determine pages count
    3 : TYPE_URL_REVIEW_UPDATE : updating reviews - links to pages for adding updated reviews
    0 : TYPE_URL_GENERAL : general type

9. Queries to extract the data

1) Query to get "REVIEWS" when tracking was added for first time:
SELECT * FROM `review` JOIN `tracking` ON `tracking`.`id` = `review`.`tracking_id` WHERE `tracking`.`status` = 5;

2) Query to get "PRODUCTS" actual for last week scraping:
SELECT `product`.* FROM `product` JOIN `crawling_queue` ON `product`.`crawling_queue_id` = `crawling_queue`.`id` WHERE `crawling_queue`.`created_at` > curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND `crawling_queue`.`status` = 5;

3) Query to get "REVIEWS" actual for last week scraping:
SELECT `review`.* FROM `review` JOIN `crawling_queue` ON `review`.`crawling_queue_id` = `crawling_queue`.`id` WHERE `crawling_queue`.`created_at` > curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND `crawling_queue`.`status` = 5;

10. Queries to clear DB for tests:

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `crawling_queue`;
TRUNCATE `crawling_url`;
TRUNCATE `product`;
TRUNCATE `review`;
UPDATE `tracking` SET `status` = 0;