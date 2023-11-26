<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121180215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD20E3D98 ON car (vis)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B26BC1124C397118 ON car_group (gid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_B26BC1124C397118 ON car_group');
        $this->addSql('DROP INDEX UNIQ_773DE69DD20E3D98 ON car');
    }
}
