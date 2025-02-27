<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227144133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_event (id INT AUTO_INCREMENT NOT NULL, error_type_id INT NOT NULL, llm_response_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, source LONGTEXT DEFAULT NULL, lineno INT DEFAULT NULL, colno INT DEFAULT NULL, error LONGTEXT DEFAULT NULL, INDEX IDX_98C396105808DDBF (error_type_id), UNIQUE INDEX UNIQ_98C39610E1741715 (llm_response_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE error_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE llmresponse (id INT AUTO_INCREMENT NOT NULL, input LONGTEXT NOT NULL, output LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_event ADD CONSTRAINT FK_98C396105808DDBF FOREIGN KEY (error_type_id) REFERENCES error_type (id)');
        $this->addSql('ALTER TABLE client_event ADD CONSTRAINT FK_98C39610E1741715 FOREIGN KEY (llm_response_id) REFERENCES llmresponse (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client_event DROP FOREIGN KEY FK_98C396105808DDBF');
        $this->addSql('ALTER TABLE client_event DROP FOREIGN KEY FK_98C39610E1741715');
        $this->addSql('DROP TABLE client_event');
        $this->addSql('DROP TABLE error_type');
        $this->addSql('DROP TABLE llmresponse');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
