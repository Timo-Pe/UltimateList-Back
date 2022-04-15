<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404085017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_platform (item_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_555F1800126F525E (item_id), INDEX IDX_555F1800FFE6496F (platform_id), PRIMARY KEY(item_id, platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mode_platform (mode_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_3A35A45077E5854A (mode_id), INDEX IDX_3A35A450FFE6496F (platform_id), PRIMARY KEY(mode_id, platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_platform ADD CONSTRAINT FK_555F1800126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_platform ADD CONSTRAINT FK_555F1800FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mode_platform ADD CONSTRAINT FK_3A35A45077E5854A FOREIGN KEY (mode_id) REFERENCES mode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mode_platform ADD CONSTRAINT FK_3A35A450FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE item_platform');
        $this->addSql('DROP TABLE mode_platform');
    }
}
