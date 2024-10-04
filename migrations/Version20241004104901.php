<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004104901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terapeuta (id INT AUTO_INCREMENT NOT NULL, id_usuario_id INT NOT NULL, titulacion VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, horario VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BDE4952C7EB2C349 (id_usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE terapeuta ADD CONSTRAINT FK_BDE4952C7EB2C349 FOREIGN KEY (id_usuario_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terapeuta DROP FOREIGN KEY FK_BDE4952C7EB2C349');
        $this->addSql('DROP TABLE terapeuta');
    }
}
