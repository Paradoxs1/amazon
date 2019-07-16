<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180714152040 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_6B2D5C527D05ABBE');
        $this->addSql('DROP INDEX IDX_306FA80A7D05ABBE ON crawling_url');
        $this->addSql('ALTER TABLE crawling_url DROP tracking_id');
        $this->addSql('ALTER TABLE crawling_queue ADD tracking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C527D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('CREATE INDEX IDX_6B2D5C527D05ABBE ON crawling_queue (tracking_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C527D05ABBE');
        $this->addSql('DROP INDEX IDX_6B2D5C527D05ABBE ON crawling_queue');
        $this->addSql('ALTER TABLE crawling_queue DROP tracking_id');
        $this->addSql('ALTER TABLE crawling_url ADD tracking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_6B2D5C527D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('CREATE INDEX IDX_306FA80A7D05ABBE ON crawling_url (tracking_id)');
    }
}
