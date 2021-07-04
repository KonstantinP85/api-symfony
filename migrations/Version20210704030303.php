<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210704030303 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create table: bookings, hotels';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bookings (id UUID NOT NULL, hotel_id UUID NOT NULL, user_id UUID NOT NULL, arrival_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration INT NOT NULL, status VARCHAR(255) NOT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A853C353243BB18 ON bookings (hotel_id)');
        $this->addSql('CREATE INDEX IDX_7A853C35A76ED395 ON bookings (user_id)');
        $this->addSql('COMMENT ON COLUMN bookings.arrival_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.create_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bookings.update_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE hotels (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, cost_one_day INT NOT NULL, address VARCHAR(255) NOT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN hotels.create_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hotels.update_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C353243BB18 FOREIGN KEY (hotel_id) REFERENCES hotels (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bookings DROP CONSTRAINT FK_7A853C353243BB18');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE hotels');
    }
}
