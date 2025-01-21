<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120131400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_ticket (purchase_id INT NOT NULL, ticket_id INT NOT NULL, INDEX IDX_4CCFAFF6558FBEB9 (purchase_id), INDEX IDX_4CCFAFF6700047D2 (ticket_id), PRIMARY KEY(purchase_id, ticket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_ticket ADD CONSTRAINT FK_4CCFAFF6558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase_ticket ADD CONSTRAINT FK_4CCFAFF6700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_ticket DROP FOREIGN KEY FK_4CCFAFF6558FBEB9');
        $this->addSql('ALTER TABLE purchase_ticket DROP FOREIGN KEY FK_4CCFAFF6700047D2');
        $this->addSql('DROP TABLE purchase_ticket');
    }
}
