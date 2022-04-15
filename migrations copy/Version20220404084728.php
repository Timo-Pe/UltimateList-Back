<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404084728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_list_item (item_id INT NOT NULL, list_item_id INT NOT NULL, INDEX IDX_560FEC07126F525E (item_id), INDEX IDX_560FEC07CE208F53 (list_item_id), PRIMARY KEY(item_id, list_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_list_item ADD CONSTRAINT FK_560FEC07126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_list_item ADD CONSTRAINT FK_560FEC07CE208F53 FOREIGN KEY (list_item_id) REFERENCES list_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item ADD mode_id INT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E77E5854A ON item (mode_id)');
        $this->addSql('ALTER TABLE list_item ADD mode_id INT NOT NULL');
        $this->addSql('ALTER TABLE list_item ADD CONSTRAINT FK_5AD5FAF777E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('CREATE INDEX IDX_5AD5FAF777E5854A ON list_item (mode_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE item_list_item');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E77E5854A');
        $this->addSql('DROP INDEX IDX_1F1B251E77E5854A ON item');
        $this->addSql('ALTER TABLE item DROP mode_id');
        $this->addSql('ALTER TABLE list_item DROP FOREIGN KEY FK_5AD5FAF777E5854A');
        $this->addSql('DROP INDEX IDX_5AD5FAF777E5854A ON list_item');
        $this->addSql('ALTER TABLE list_item DROP mode_id');
    }
}
