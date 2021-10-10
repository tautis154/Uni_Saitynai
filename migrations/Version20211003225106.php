<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211003225106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `doctor` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1FC0F36AF85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, doctor_id INT DEFAULT NULL, client_name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, rating INT NOT NULL, INDEX IDX_794381C687F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, doctor_id INT DEFAULT NULL, medicine_id INT DEFAULT NULL, client_name VARCHAR(255) NOT NULL, is_admitted TINYINT(1) NOT NULL, is_finished TINYINT(1) NOT NULL, date DATETIME NOT NULL, INDEX IDX_437EE93987F4FB17 (doctor_id), INDEX IDX_437EE9392F7D140A (medicine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C687F4FB17 FOREIGN KEY (doctor_id) REFERENCES `doctor` (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93987F4FB17 FOREIGN KEY (doctor_id) REFERENCES `doctor` (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE9392F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C687F4FB17');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE93987F4FB17');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE9392F7D140A');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE `doctor`');
        $this->addSql('DROP TABLE medicine');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE visit');
    }
}
