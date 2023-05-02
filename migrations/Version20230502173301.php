<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502173301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B876C4DDA');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B876C4DDA');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
    }
}
