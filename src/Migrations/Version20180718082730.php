<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180718082730 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD crawling_queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C65AA871A8 FOREIGN KEY (crawling_queue_id) REFERENCES crawling_queue (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C65AA871A8 ON review (crawling_queue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C65AA871A8');
        $this->addSql('DROP INDEX UNIQ_794381C65AA871A8 ON review');
        $this->addSql('ALTER TABLE review DROP crawling_queue_id');
    }
}
