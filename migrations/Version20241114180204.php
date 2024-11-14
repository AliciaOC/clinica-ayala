<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114180204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita ADD terapeuta_id INT NOT NULL, ADD cliente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62FC472237 FOREIGN KEY (terapeuta_id) REFERENCES terapeuta (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('CREATE INDEX IDX_3E379A62FC472237 ON cita (terapeuta_id)');
        $this->addSql('CREATE INDEX IDX_3E379A62DE734E51 ON cita (cliente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62FC472237');
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62DE734E51');
        $this->addSql('DROP INDEX IDX_3E379A62FC472237 ON cita');
        $this->addSql('DROP INDEX IDX_3E379A62DE734E51 ON cita');
        $this->addSql('ALTER TABLE cita DROP terapeuta_id, DROP cliente_id');
    }
}
