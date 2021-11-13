<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211021024448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE booking_history (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', booking_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', who VARCHAR(255) NOT NULL, new_value VARCHAR(255) NOT NULL, INDEX IDX_7D04356B3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking_history ADD CONSTRAINT FK_7D04356B3301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE booking_history');
    }
}
