<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216120156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE history_car (id INT AUTO_INCREMENT NOT NULL, replaced_car_id INT DEFAULT NULL, history_car_group_id INT DEFAULT NULL, vis VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, note VARCHAR(255) DEFAULT NULL, is_damaged SMALLINT NOT NULL, UNIQUE INDEX UNIQ_9992BB52A89D86D0 (replaced_car_id), INDEX IDX_9992BB5245921FB8 (history_car_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history_car_group (id INT AUTO_INCREMENT NOT NULL, gid VARCHAR(255) NOT NULL, front_license_plate VARCHAR(255) NOT NULL, back_license_plate VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, import_time DATETIME NOT NULL, export_time DATETIME NOT NULL, destination VARCHAR(255) DEFAULT NULL, receiver VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE history_car ADD CONSTRAINT FK_9992BB52A89D86D0 FOREIGN KEY (replaced_car_id) REFERENCES history_car (id)');
        $this->addSql('ALTER TABLE history_car ADD CONSTRAINT FK_9992BB5245921FB8 FOREIGN KEY (history_car_group_id) REFERENCES history_car_group (id)');
        $this->addSql('ALTER TABLE log CHANGE object_class object_class VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_car DROP FOREIGN KEY FK_9992BB52A89D86D0');
        $this->addSql('ALTER TABLE history_car DROP FOREIGN KEY FK_9992BB5245921FB8');
        $this->addSql('DROP TABLE history_car');
        $this->addSql('DROP TABLE history_car_group');
        $this->addSql('ALTER TABLE log CHANGE object_class object_class VARCHAR(255) DEFAULT NULL');
    }
}
