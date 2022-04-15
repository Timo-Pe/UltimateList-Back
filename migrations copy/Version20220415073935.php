<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415073935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, mode_id INT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, release_date DATE NOT NULL, productor VARCHAR(64) DEFAULT NULL, autor VARCHAR(64) DEFAULT NULL, host VARCHAR(64) DEFAULT NULL, developer VARCHAR(64) DEFAULT NULL, editor VARCHAR(64) DEFAULT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_1F1B251E77E5854A (mode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_platform (item_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_555F1800126F525E (item_id), INDEX IDX_555F1800FFE6496F (platform_id), PRIMARY KEY(item_id, platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_tag (item_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E49CCCB1126F525E (item_id), INDEX IDX_E49CCCB1BAD26311 (tag_id), PRIMARY KEY(item_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE list_item (id INT AUTO_INCREMENT NOT NULL, mode_id INT NOT NULL, user_id INT NOT NULL, item_id INT DEFAULT NULL, item_added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', item_status INT NOT NULL, item_comment LONGTEXT DEFAULT NULL, item_rating INT DEFAULT NULL, INDEX IDX_5AD5FAF777E5854A (mode_id), INDEX IDX_5AD5FAF7A76ED395 (user_id), INDEX IDX_5AD5FAF7126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, slug VARCHAR(64) DEFAULT NULL, color VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mode_platform (mode_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_3A35A45077E5854A (mode_id), INDEX IDX_3A35A450FFE6496F (platform_id), PRIMARY KEY(mode_id, platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE platform (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, color VARCHAR(64) DEFAULT \'#7068F4\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE item_platform ADD CONSTRAINT FK_555F1800126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_platform ADD CONSTRAINT FK_555F1800FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_tag ADD CONSTRAINT FK_E49CCCB1126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_tag ADD CONSTRAINT FK_E49CCCB1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE list_item ADD CONSTRAINT FK_5AD5FAF777E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE list_item ADD CONSTRAINT FK_5AD5FAF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE list_item ADD CONSTRAINT FK_5AD5FAF7126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE mode_platform ADD CONSTRAINT FK_3A35A45077E5854A FOREIGN KEY (mode_id) REFERENCES mode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mode_platform ADD CONSTRAINT FK_3A35A450FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_platform DROP FOREIGN KEY FK_555F1800126F525E');
        $this->addSql('ALTER TABLE item_tag DROP FOREIGN KEY FK_E49CCCB1126F525E');
        $this->addSql('ALTER TABLE list_item DROP FOREIGN KEY FK_5AD5FAF7126F525E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E77E5854A');
        $this->addSql('ALTER TABLE list_item DROP FOREIGN KEY FK_5AD5FAF777E5854A');
        $this->addSql('ALTER TABLE mode_platform DROP FOREIGN KEY FK_3A35A45077E5854A');
        $this->addSql('ALTER TABLE item_platform DROP FOREIGN KEY FK_555F1800FFE6496F');
        $this->addSql('ALTER TABLE mode_platform DROP FOREIGN KEY FK_3A35A450FFE6496F');
        $this->addSql('ALTER TABLE item_tag DROP FOREIGN KEY FK_E49CCCB1BAD26311');
        $this->addSql('ALTER TABLE list_item DROP FOREIGN KEY FK_5AD5FAF7A76ED395');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_platform');
        $this->addSql('DROP TABLE item_tag');
        $this->addSql('DROP TABLE list_item');
        $this->addSql('DROP TABLE mode');
        $this->addSql('DROP TABLE mode_platform');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
    }
}
