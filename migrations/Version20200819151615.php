<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200819151615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conceptor (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD conceptor_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADEA713641 FOREIGN KEY (conceptor_id) REFERENCES conceptor (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADEA713641 ON product (conceptor_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADEA713641');
        $this->addSql('DROP TABLE conceptor');
        $this->addSql('DROP INDEX IDX_D34A04ADEA713641 ON product');
        $this->addSql('ALTER TABLE product DROP conceptor_id');
    }
}
