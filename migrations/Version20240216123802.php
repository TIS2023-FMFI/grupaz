<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216123802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_car_group CHANGE front_license_plate front_license_plate VARCHAR(255) DEFAULT NULL, CHANGE back_license_plate back_license_plate VARCHAR(255) DEFAULT NULL, CHANGE export_time export_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_car_group CHANGE front_license_plate front_license_plate VARCHAR(255) NOT NULL, CHANGE back_license_plate back_license_plate VARCHAR(255) NOT NULL, CHANGE export_time export_time DATETIME NOT NULL');
    }
}
