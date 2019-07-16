<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180709073500 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE crawling_queue (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, type SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crawling_url ADD queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A477B5BAE FOREIGN KEY (queue_id) REFERENCES crawling_queue (id)');
        $this->addSql('CREATE INDEX IDX_306FA80A477B5BAE ON crawling_url (queue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A477B5BAE');
        $this->addSql('DROP TABLE crawling_queue');
        $this->addSql('DROP INDEX IDX_306FA80A477B5BAE ON crawling_url');
        $this->addSql('ALTER TABLE crawling_url DROP queue_id');
    }
}
