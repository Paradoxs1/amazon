<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180719143836 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C65AA871A8');
        $this->addSql('DROP INDEX IDX_794381C65AA871A8 ON review');
        $this->addSql('ALTER TABLE review CHANGE crawling_queue_id crawling_url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6D0050AFE FOREIGN KEY (crawling_url_id) REFERENCES crawling_url (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_794381C6D0050AFE ON review (crawling_url_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6D0050AFE');
        $this->addSql('DROP INDEX IDX_794381C6D0050AFE ON review');
        $this->addSql('ALTER TABLE review CHANGE crawling_url_id crawling_queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C65AA871A8 FOREIGN KEY (crawling_queue_id) REFERENCES crawling_queue (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_794381C65AA871A8 ON review (crawling_queue_id)');
    }
}
