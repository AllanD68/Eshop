<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200630202033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, purchase_order_id INT NOT NULL, product_id INT DEFAULT NULL, qty INT NOT NULL, INDEX IDX_BA388B7A45D7E6A (purchase_order_id), INDEX IDX_BA388B74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, release_date DATE NOT NULL, stock INT NOT NULL, price DOUBLE PRECISION NOT NULL, edition VARCHAR(255) NOT NULL, new TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_order DATE DEFAULT NULL, adress VARCHAR(255) NOT NULL, pc VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, validated TINYINT(1) NOT NULL, INDEX IDX_21E210B2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT DEFAULT NULL, note INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, inscription_date DATE DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_order (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_order ADD CONSTRAINT FK_21E210B2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A45D7E6A');
        $this->addSql('ALTER TABLE purchase_order DROP FOREIGN KEY FK_21E210B2A76ED395');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchase_order');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
    }
}
