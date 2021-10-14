<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211013125231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bookings (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', hotel_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', arrival_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', duration INT NOT NULL, status VARCHAR(255) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7A853C353243BB18 (hotel_id), INDEX IDX_7A853C35A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_confirmation_tokens (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', token VARCHAR(255) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expire_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7C168F29A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotels (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, cost_one_day INT NOT NULL, address VARCHAR(255) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, login_attempt_counter INT NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C353243BB18 FOREIGN KEY (hotel_id) REFERENCES hotels (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_confirmation_tokens ADD CONSTRAINT FK_7C168F29A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C353243BB18');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35A76ED395');
        $this->addSql('ALTER TABLE email_confirmation_tokens DROP FOREIGN KEY FK_7C168F29A76ED395');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE email_confirmation_tokens');
        $this->addSql('DROP TABLE hotels');
        $this->addSql('DROP TABLE users');
    }
}
