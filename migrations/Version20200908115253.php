<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200908115253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_order_product (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, purchase_orders_id INT DEFAULT NULL, qty INT NOT NULL, INDEX IDX_F32214F96C8A81A9 (products_id), INDEX IDX_F32214F94AB2681D (purchase_orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F96C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F94AB2681D FOREIGN KEY (purchase_orders_id) REFERENCES purchase_order (id)');
        $this->addSql('DROP TABLE cart');
        $this->addSql('ALTER TABLE picture CHANGE product_id product_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, purchase_order_id INT NOT NULL, product_id INT DEFAULT NULL, qty INT NOT NULL, INDEX IDX_BA388B74584665A (product_id), INDEX IDX_BA388B7A45D7E6A (purchase_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_order (id)');
        $this->addSql('DROP TABLE purchase_order_product');
        $this->addSql('ALTER TABLE picture CHANGE product_id product_id INT NOT NULL');
    }
}
