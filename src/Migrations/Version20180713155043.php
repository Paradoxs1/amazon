<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180713155043 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A477B5BAE');
        $this->addSql('DROP INDEX IDX_306FA80A477B5BAE ON crawling_url');
        $this->addSql('ALTER TABLE crawling_url CHANGE queue_id crawling_queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A5AA871A8 FOREIGN KEY (crawling_queue_id) REFERENCES crawling_queue (id)');
        $this->addSql('CREATE INDEX IDX_306FA80A5AA871A8 ON crawling_url (crawling_queue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A5AA871A8');
        $this->addSql('DROP INDEX IDX_306FA80A5AA871A8 ON crawling_url');
        $this->addSql('ALTER TABLE crawling_url CHANGE crawling_queue_id queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A477B5BAE FOREIGN KEY (queue_id) REFERENCES crawling_queue (id)');
        $this->addSql('CREATE INDEX IDX_306FA80A477B5BAE ON crawling_url (queue_id)');
    }
}
