<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180628093804 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE crawling_url (id INT AUTO_INCREMENT NOT NULL, tracking_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_6B2D5C527D05ABBE (tracking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, tracking_id INT DEFAULT NULL, asin VARCHAR(255) NOT NULL, total_review_count INT DEFAULT NULL, total_review_count_verified INT DEFAULT NULL, total_star_rating DOUBLE PRECISION DEFAULT NULL, total_five INT DEFAULT NULL, total_five_verified INT DEFAULT NULL, total_four INT DEFAULT NULL, total_four_verified INT DEFAULT NULL, total_three INT DEFAULT NULL, total_three_verified INT DEFAULT NULL, total_two INT DEFAULT NULL, total_two_verified INT DEFAULT NULL, total_one INT DEFAULT NULL, total_one_verified INT DEFAULT NULL, total_child_review_count INT DEFAULT NULL, total_child_review_count_verified INT DEFAULT NULL, total_child_five INT DEFAULT NULL, total_child_five_verified INT DEFAULT NULL, total_child_four INT DEFAULT NULL, total_child_four_verified INT DEFAULT NULL, total_child_three INT DEFAULT NULL, total_child_three_verified INT DEFAULT NULL, total_child_two INT DEFAULT NULL, total_child_two_verified INT DEFAULT NULL, total_child_one INT DEFAULT NULL, total_child_one_verified INT DEFAULT NULL, is_downloaded SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D34A04AD7D05ABBE (tracking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, tracking_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, asin VARCHAR(255) NOT NULL, rate SMALLINT NOT NULL, author VARCHAR(255) NOT NULL, helpful_count INT NOT NULL, verified SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, content LONGTEXT DEFAULT NULL, date_review DATETIME NOT NULL, active SMALLINT NOT NULL, review_id VARCHAR(255) NOT NULL, is_downloaded SMALLINT NOT NULL, UNIQUE INDEX UNIQ_794381C63E2E969B (review_id), INDEX IDX_794381C64584665A (product_id), INDEX IDX_794381C67D05ABBE (tracking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking (id INT AUTO_INCREMENT NOT NULL, asin VARCHAR(255) NOT NULL, parent VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, UNIQUE INDEX UNIQ_A87C621CEA5C05C2 (asin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crawling_url ADD CONSTRAINT FK_6B2D5C527D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE crawling_url DROP FOREIGN KEY FK_6B2D5C527D05ABBE');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7D05ABBE');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67D05ABBE');
        $this->addSql('DROP TABLE crawling_url');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tracking');
    }
}
