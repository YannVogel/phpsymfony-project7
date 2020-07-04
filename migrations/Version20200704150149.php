<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200704150149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE battery (id INT AUTO_INCREMENT NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color_product (color_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_80DC89837ADA1FB5 (color_id), INDEX IDX_80DC89834584665A (product_id), PRIMARY KEY(color_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE illustration (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_D67B9A424584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE os (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, battery_id INT NOT NULL, brand_id INT NOT NULL, os_id INT NOT NULL, screen_resolution_id INT NOT NULL, screen_technology_id INT NOT NULL, sim_size_id INT NOT NULL, storage_id INT NOT NULL, INDEX IDX_D34A04AD19A19CFC (battery_id), INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD3DCA04D1 (os_id), INDEX IDX_D34A04AD6C8C2577 (screen_resolution_id), INDEX IDX_D34A04AD3C18352E (screen_technology_id), INDEX IDX_D34A04ADD47E5A64 (sim_size_id), INDEX IDX_D34A04AD5CC5DB90 (storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_wireless_technology (product_id INT NOT NULL, wireless_technology_id INT NOT NULL, INDEX IDX_FA118BCC4584665A (product_id), INDEX IDX_FA118BCCEF5FC7A (wireless_technology_id), PRIMARY KEY(product_id, wireless_technology_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE screen_resolution (id INT AUTO_INCREMENT NOT NULL, resolution VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE screen_technology (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sim_size (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, civility VARCHAR(1) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, age INT NOT NULL, city VARCHAR(255) NOT NULL, INDEX IDX_8D93D64919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wireless_technology (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE color_product ADD CONSTRAINT FK_80DC89837ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE color_product ADD CONSTRAINT FK_80DC89834584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A424584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD19A19CFC FOREIGN KEY (battery_id) REFERENCES battery (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3DCA04D1 FOREIGN KEY (os_id) REFERENCES os (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD6C8C2577 FOREIGN KEY (screen_resolution_id) REFERENCES screen_resolution (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3C18352E FOREIGN KEY (screen_technology_id) REFERENCES screen_technology (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADD47E5A64 FOREIGN KEY (sim_size_id) REFERENCES sim_size (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD5CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE product_wireless_technology ADD CONSTRAINT FK_FA118BCC4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_wireless_technology ADD CONSTRAINT FK_FA118BCCEF5FC7A FOREIGN KEY (wireless_technology_id) REFERENCES wireless_technology (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD19A19CFC');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE color_product DROP FOREIGN KEY FK_80DC89837ADA1FB5');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD3DCA04D1');
        $this->addSql('ALTER TABLE color_product DROP FOREIGN KEY FK_80DC89834584665A');
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A424584665A');
        $this->addSql('ALTER TABLE product_wireless_technology DROP FOREIGN KEY FK_FA118BCC4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD6C8C2577');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD3C18352E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADD47E5A64');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD5CC5DB90');
        $this->addSql('ALTER TABLE product_wireless_technology DROP FOREIGN KEY FK_FA118BCCEF5FC7A');
        $this->addSql('DROP TABLE battery');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE color_product');
        $this->addSql('DROP TABLE illustration');
        $this->addSql('DROP TABLE os');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_wireless_technology');
        $this->addSql('DROP TABLE screen_resolution');
        $this->addSql('DROP TABLE screen_technology');
        $this->addSql('DROP TABLE sim_size');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wireless_technology');
    }
}
