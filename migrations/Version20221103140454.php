<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221103140454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention_user (intervention_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_822CCE8B8EAE3863 (intervention_id), INDEX IDX_822CCE8BA76ED395 (user_id), PRIMARY KEY(intervention_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intervention_user ADD CONSTRAINT FK_822CCE8B8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intervention_user ADD CONSTRAINT FK_822CCE8BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABA76ED395');
        $this->addSql('DROP INDEX IDX_D11814ABA76ED395 ON intervention');
        $this->addSql('ALTER TABLE intervention DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention_user DROP FOREIGN KEY FK_822CCE8B8EAE3863');
        $this->addSql('ALTER TABLE intervention_user DROP FOREIGN KEY FK_822CCE8BA76ED395');
        $this->addSql('DROP TABLE intervention_user');
        $this->addSql('ALTER TABLE intervention ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D11814ABA76ED395 ON intervention (user_id)');
    }
}
