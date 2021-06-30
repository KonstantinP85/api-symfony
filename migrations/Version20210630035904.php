<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210630035904 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create table email_confirmation_tokens';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE email_confirmation_tokens (id UUID NOT NULL, user_id UUID DEFAULT NULL, token VARCHAR(255) NOT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expire_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C168F29A76ED395 ON email_confirmation_tokens (user_id)');
        $this->addSql('COMMENT ON COLUMN email_confirmation_tokens.create_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN email_confirmation_tokens.expire_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE email_confirmation_tokens ADD CONSTRAINT FK_7C168F29A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE email_confirmation_tokens');
    }
}
