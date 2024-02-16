<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216123219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_car DROP FOREIGN KEY FK_9992BB52A89D86D0');
        $this->addSql('DROP INDEX UNIQ_9992BB52A89D86D0 ON history_car');
        $this->addSql('ALTER TABLE history_car ADD replaced_car VARCHAR(255) DEFAULT NULL, DROP replaced_car_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_car ADD replaced_car_id INT DEFAULT NULL, DROP replaced_car');
        $this->addSql('ALTER TABLE history_car ADD CONSTRAINT FK_9992BB52A89D86D0 FOREIGN KEY (replaced_car_id) REFERENCES history_car (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9992BB52A89D86D0 ON history_car (replaced_car_id)');
    }
}
