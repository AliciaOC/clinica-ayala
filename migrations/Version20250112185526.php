<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112185526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62DE734E51');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62DE734E51');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
    }
}
