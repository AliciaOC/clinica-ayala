<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105190026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terapeuta_cliente (terapeuta_id INT NOT NULL, cliente_id INT NOT NULL, INDEX IDX_F6FFC1EBFC472237 (terapeuta_id), INDEX IDX_F6FFC1EBDE734E51 (cliente_id), PRIMARY KEY(terapeuta_id, cliente_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE terapeuta_cliente ADD CONSTRAINT FK_F6FFC1EBFC472237 FOREIGN KEY (terapeuta_id) REFERENCES terapeuta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE terapeuta_cliente ADD CONSTRAINT FK_F6FFC1EBDE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terapeuta_cliente DROP FOREIGN KEY FK_F6FFC1EBFC472237');
        $this->addSql('ALTER TABLE terapeuta_cliente DROP FOREIGN KEY FK_F6FFC1EBDE734E51');
        $this->addSql('DROP TABLE terapeuta_cliente');
    }
}
