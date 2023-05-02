<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502145057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD8BAC62AF');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BDA6A219');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD8BAC62AF');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BDA6A219');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
    }
}
