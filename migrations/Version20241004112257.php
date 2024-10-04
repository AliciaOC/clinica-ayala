<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004112257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tratamiento (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, descripcion LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tratamiento_terapeuta (tratamiento_id INT NOT NULL, terapeuta_id INT NOT NULL, INDEX IDX_FB6F7DEC44168F7D (tratamiento_id), INDEX IDX_FB6F7DECFC472237 (terapeuta_id), PRIMARY KEY(tratamiento_id, terapeuta_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tratamiento_terapeuta ADD CONSTRAINT FK_FB6F7DEC44168F7D FOREIGN KEY (tratamiento_id) REFERENCES tratamiento (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tratamiento_terapeuta ADD CONSTRAINT FK_FB6F7DECFC472237 FOREIGN KEY (terapeuta_id) REFERENCES terapeuta (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tratamiento_terapeuta DROP FOREIGN KEY FK_FB6F7DEC44168F7D');
        $this->addSql('ALTER TABLE tratamiento_terapeuta DROP FOREIGN KEY FK_FB6F7DECFC472237');
        $this->addSql('DROP TABLE tratamiento');
        $this->addSql('DROP TABLE tratamiento_terapeuta');
    }
}
