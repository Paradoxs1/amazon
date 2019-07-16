<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180716053914 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C52727ACA70');
        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C527D05ABBE');
        $this->addSql('ALTER TABLE crawling_queue CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C52727ACA70 FOREIGN KEY (parent_id) REFERENCES crawling_queue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C527D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A4584665A');
        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A5AA871A8');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A5AA871A8 FOREIGN KEY (crawling_queue_id) REFERENCES crawling_queue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7D05ABBE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67D05ABBE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C527D05ABBE');
        $this->addSql('ALTER TABLE crawling_queue DROP FOREIGN KEY FK_6B2D5C52727ACA70');
        $this->addSql('ALTER TABLE crawling_queue CHANGE status status SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C527D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('ALTER TABLE crawling_queue ADD CONSTRAINT FK_6B2D5C52727ACA70 FOREIGN KEY (parent_id) REFERENCES crawling_queue (id)');
        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A4584665A');
        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_306FA80A5AA871A8');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_306FA80A5AA871A8 FOREIGN KEY (crawling_queue_id) REFERENCES crawling_queue (id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7D05ABBE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67D05ABBE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
    }
}
