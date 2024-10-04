<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004105555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terapeuta DROP FOREIGN KEY FK_BDE4952C7EB2C349');
        $this->addSql('DROP INDEX UNIQ_BDE4952C7EB2C349 ON terapeuta');
        $this->addSql('ALTER TABLE terapeuta CHANGE id_usuario_id usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE terapeuta ADD CONSTRAINT FK_BDE4952CDB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDE4952CDB38439E ON terapeuta (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terapeuta DROP FOREIGN KEY FK_BDE4952CDB38439E');
        $this->addSql('DROP INDEX UNIQ_BDE4952CDB38439E ON terapeuta');
        $this->addSql('ALTER TABLE terapeuta CHANGE usuario_id id_usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE terapeuta ADD CONSTRAINT FK_BDE4952C7EB2C349 FOREIGN KEY (id_usuario_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDE4952C7EB2C349 ON terapeuta (id_usuario_id)');
    }
}
