<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230420132337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, availability VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, techno VARCHAR(20) DEFAULT NULL, INDEX IDX_94D4687F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence_user (competence_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CA0BDC5215761DAB (competence_id), INDEX IDX_CA0BDC52A76ED395 (user_id), PRIMARY KEY(competence_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date INT DEFAULT NULL, techno VARCHAR(255) DEFAULT NULL, github VARCHAR(100) DEFAULT NULL, url_project VARCHAR(100) DEFAULT NULL, upload VARCHAR(100) DEFAULT NULL, created_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, INDEX IDX_2FB3D0EEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, year_exp_id INT DEFAULT NULL, availability_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(20) NOT NULL, lastname VARCHAR(35) NOT NULL, town VARCHAR(25) DEFAULT NULL, cv VARCHAR(100) DEFAULT NULL, github VARCHAR(100) DEFAULT NULL, linkedin VARCHAR(100) DEFAULT NULL, portfolio VARCHAR(100) DEFAULT NULL, profile_picture VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, pricing INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649BE04EA9 (job_id), INDEX IDX_8D93D649CA7201A3 (year_exp_id), INDEX IDX_8D93D64961778466 (availability_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_match (id INT AUTO_INCREMENT NOT NULL, user_matcher_id INT DEFAULT NULL, user_matched_id INT DEFAULT NULL, status VARCHAR(15) NOT NULL, date DATETIME NOT NULL, INDEX IDX_98993E5D54EB7C90 (user_matcher_id), INDEX IDX_98993E5D219974D3 (user_matched_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE year_experience (id INT AUTO_INCREMENT NOT NULL, year_exp VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE competence_user ADD CONSTRAINT FK_CA0BDC5215761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence_user ADD CONSTRAINT FK_CA0BDC52A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CA7201A3 FOREIGN KEY (year_exp_id) REFERENCES year_experience (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64961778466 FOREIGN KEY (availability_id) REFERENCES availability (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_match ADD CONSTRAINT FK_98993E5D54EB7C90 FOREIGN KEY (user_matcher_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_match ADD CONSTRAINT FK_98993E5D219974D3 FOREIGN KEY (user_matched_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687F12469DE2');
        $this->addSql('ALTER TABLE competence_user DROP FOREIGN KEY FK_CA0BDC5215761DAB');
        $this->addSql('ALTER TABLE competence_user DROP FOREIGN KEY FK_CA0BDC52A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649BE04EA9');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CA7201A3');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64961778466');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE user_match DROP FOREIGN KEY FK_98993E5D54EB7C90');
        $this->addSql('ALTER TABLE user_match DROP FOREIGN KEY FK_98993E5D219974D3');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE competence_user');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE user_match');
        $this->addSql('DROP TABLE year_experience');
    }
}
