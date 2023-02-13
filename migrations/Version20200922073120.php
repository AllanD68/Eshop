<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922073120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, inscription_date DATE DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, activation_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, adress VARCHAR(255) NOT NULL, pc VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, validated TINYINT(1) NOT NULL, total INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_21E210B2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE platform (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conceptor (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, conceptor_id INT NOT NULL, category_id INT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, release_date DATE NOT NULL, stock INT NOT NULL, price DOUBLE PRECISION NOT NULL, new TINYINT(1) NOT NULL, INDEX IDX_D34A04ADEA713641 (conceptor_id), INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, product_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, note INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C64584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_order_product (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, purchase_orders_id INT DEFAULT NULL, qty INT NOT NULL, INDEX IDX_F32214F96C8A81A9 (products_id), INDEX IDX_F32214F94AB2681D (purchase_orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_genre (product_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_220C48A44584665A (product_id), INDEX IDX_220C48A44296D31F (genre_id), PRIMARY KEY(product_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE platform_product (platform_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C67ACEFFFFE6496F (platform_id), INDEX IDX_C67ACEFF4584665A (product_id), PRIMARY KEY(platform_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_16DB4F894584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_order ADD CONSTRAINT FK_21E210B2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADEA713641 FOREIGN KEY (conceptor_id) REFERENCES conceptor (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F96C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_order_product ADD CONSTRAINT FK_F32214F94AB2681D FOREIGN KEY (purchase_orders_id) REFERENCES purchase_order (id)');
        $this->addSql('ALTER TABLE product_genre ADD CONSTRAINT FK_220C48A44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_genre ADD CONSTRAINT FK_220C48A44296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE platform_product ADD CONSTRAINT FK_C67ACEFFFFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE platform_product ADD CONSTRAINT FK_C67ACEFF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F894584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADEA713641');
        $this->addSql('ALTER TABLE product_genre DROP FOREIGN KEY FK_220C48A44296D31F');
        $this->addSql('ALTER TABLE platform_product DROP FOREIGN KEY FK_C67ACEFFFFE6496F');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F894584665A');
        $this->addSql('ALTER TABLE platform_product DROP FOREIGN KEY FK_C67ACEFF4584665A');
        $this->addSql('ALTER TABLE product_genre DROP FOREIGN KEY FK_220C48A44584665A');
        $this->addSql('ALTER TABLE purchase_order_product DROP FOREIGN KEY FK_F32214F96C8A81A9');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE purchase_order_product DROP FOREIGN KEY FK_F32214F94AB2681D');
        $this->addSql('ALTER TABLE purchase_order DROP FOREIGN KEY FK_21E210B2A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE conceptor');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE platform_product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_genre');
        $this->addSql('DROP TABLE purchase_order');
        $this->addSql('DROP TABLE purchase_order_product');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
    }
}
