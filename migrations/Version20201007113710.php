<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007113710 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_order DROP validated, CHANGE total total INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE purchase_order ADD validated TINYINT(1) NOT NULL, CHANGE total total INT DEFAULT NULL');
    }
}
