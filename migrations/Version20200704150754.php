<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200704150754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD name VARCHAR(255) NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD dual_sim TINYINT(1) NOT NULL, ADD micro_sd TINYINT(1) NOT NULL, ADD screen_size DOUBLE PRECISION NOT NULL, ADD camera_resolution DOUBLE PRECISION NOT NULL, ADD weight DOUBLE PRECISION NOT NULL, ADD usb_type_c TINYINT(1) NOT NULL, ADD years_of_warranty INT NOT NULL, ADD jack_plug TINYINT(1) NOT NULL, ADD front_camera TINYINT(1) NOT NULL, ADD back_camera TINYINT(1) NOT NULL, ADD ram INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP name, DROP price, DROP dual_sim, DROP micro_sd, DROP screen_size, DROP camera_resolution, DROP weight, DROP usb_type_c, DROP years_of_warranty, DROP jack_plug, DROP front_camera, DROP back_camera, DROP ram');
    }
}
