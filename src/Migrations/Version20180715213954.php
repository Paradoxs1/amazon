<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180715213954 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_queue ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C52727ACA70 FOREIGN KEY (parent_id) REFERENCES crawling_queue (id)');
        $this->addSql('CREATE INDEX IDX_6B2D5C52727ACA70 ON crawling_queue (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C52727ACA70');
        $this->addSql('DROP INDEX IDX_6B2D5C52727ACA70 ON crawling_queue');
        $this->addSql('ALTER TABLE crawling_queue DROP parent_id');
    }
}
