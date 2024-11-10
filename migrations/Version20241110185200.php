<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110185200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terapeuta_horario (terapeuta_id INT NOT NULL, horario_id INT NOT NULL, INDEX IDX_E0BB096DFC472237 (terapeuta_id), INDEX IDX_E0BB096D4959F1BA (horario_id), PRIMARY KEY(terapeuta_id, horario_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE terapeuta_horario ADD CONSTRAINT FK_E0BB096DFC472237 FOREIGN KEY (terapeuta_id) REFERENCES terapeuta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE terapeuta_horario ADD CONSTRAINT FK_E0BB096D4959F1BA FOREIGN KEY (horario_id) REFERENCES horario (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terapeuta_horario DROP FOREIGN KEY FK_E0BB096DFC472237');
        $this->addSql('ALTER TABLE terapeuta_horario DROP FOREIGN KEY FK_E0BB096D4959F1BA');
        $this->addSql('DROP TABLE terapeuta_horario');
    }
}
