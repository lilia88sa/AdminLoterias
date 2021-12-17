<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211004193701 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql('ALTER TABLE category CHANGE section_id section_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE title_es title_es VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cliente CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE post_id post_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications CHANGE user_id user_id INT DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE view_counter CHANGE post_id post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entities CHANGE user_id user_id INT DEFAULT NULL, CHANGE comercial_name comercial_name VARCHAR(255) DEFAULT NULL, CHANGE code code VARCHAR(255) DEFAULT NULL, CHANGE schedule schedule VARCHAR(255) DEFAULT NULL, CHANGE schedule_public schedule_public VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE file CHANGE category_id category_id INT DEFAULT NULL, CHANGE section_id section_id INT DEFAULT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE key_words_id key_words_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL, CHANGE juego_id juego_id INT DEFAULT NULL, CHANGE image_id image_id INT DEFAULT NULL, CHANGE filename filename VARCHAR(255) DEFAULT NULL, CHANGE size size INT DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE juego ADD bote_destacado TINYINT(1) DEFAULT NULL, CHANGE bote bote VARCHAR(50) DEFAULT NULL, CHANGE fecha_bote fecha_bote DATETIME DEFAULT NULL, CHANGE order_element order_element INT DEFAULT NULL, CHANGE num_sorteo num_sorteo VARCHAR(255) DEFAULT NULL, CHANGE order_home order_home INT DEFAULT NULL, CHANGE url_juego url_juego VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE key_words CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE title_es title_es VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE numero_premiado CHANGE fecha fecha DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE order_clasification CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pdf CHANGE post_id post_id INT DEFAULT NULL, CHANGE pdf_url pdf_url VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE category_id category_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE audio audio VARCHAR(255) DEFAULT NULL, CHANGE video video VARCHAR(255) DEFAULT NULL, CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE clasification clasification VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE title_es title_es VARCHAR(255) DEFAULT NULL, CHANGE optional_date optional_date DATETIME DEFAULT NULL, CHANGE views views INT DEFAULT NULL, CHANGE translation_update translation_update DATETIME DEFAULT NULL, CHANGE post_update post_update DATETIME DEFAULT NULL, CHANGE last_user_translation last_user_translation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rating CHANGE post_id post_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE result ADD pos_year INT DEFAULT NULL, CHANGE estrellas estrellas VARCHAR(50) DEFAULT NULL, CHANGE reintegros reintegros VARCHAR(50) DEFAULT NULL, CHANGE complementos complementos VARCHAR(50) DEFAULT NULL, CHANGE clave clave VARCHAR(50) DEFAULT NULL, CHANGE fraccion_serie fraccion_serie VARCHAR(50) DEFAULT NULL, CHANGE caballo caballo VARCHAR(50) DEFAULT NULL, CHANGE millon_joker millon_joker VARCHAR(50) DEFAULT NULL, CHANGE bote bote VARCHAR(100) DEFAULT NULL, CHANGE fecha fecha DATE DEFAULT NULL, CHANGE url_lectura url_lectura VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE section CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE title_es title_es VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE object_id object_id VARCHAR(64) DEFAULT NULL, CHANGE data data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE token_logout_change_date token_logout_change_date INT DEFAULT NULL, CHANGE version version INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       /* $this->addSql('CREATE TABLE consulta (id INT AUTO_INCREMENT NOT NULL, consulta LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, descripcion LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');*/
        $this->addSql('ALTER TABLE category CHANGE section_id section_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title_es title_es VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE cliente CHANGE nombre nombre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE comments CHANGE post_id post_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE entities CHANGE user_id user_id INT DEFAULT NULL, CHANGE comercial_name comercial_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE code code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE schedule schedule VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE schedule_public schedule_public VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE file CHANGE category_id category_id INT DEFAULT NULL, CHANGE section_id section_id INT DEFAULT NULL, CHANGE post_id post_id INT DEFAULT NULL, CHANGE key_words_id key_words_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL, CHANGE juego_id juego_id INT DEFAULT NULL, CHANGE image_id image_id INT DEFAULT NULL, CHANGE filename filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE size size INT DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE juego DROP bote_destacado, CHANGE bote bote VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fecha_bote fecha_bote DATETIME DEFAULT \'NULL\', CHANGE order_element order_element INT DEFAULT NULL, CHANGE num_sorteo num_sorteo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE order_home order_home INT DEFAULT NULL, CHANGE url_juego url_juego VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE key_words CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title_es title_es VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE logs CHANGE object_id object_id VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE data data LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE username username VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE notifications CHANGE user_id user_id INT DEFAULT NULL, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE numero_premiado CHANGE fecha fecha DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE order_clasification CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE pdf CHANGE post_id post_id INT DEFAULT NULL, CHANGE pdf_url pdf_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE post CHANGE category_id category_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE audio audio VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE video video VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE author author VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE clasification clasification VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title_es title_es VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE optional_date optional_date DATETIME DEFAULT \'NULL\', CHANGE views views INT DEFAULT NULL, CHANGE translation_update translation_update DATETIME DEFAULT \'NULL\', CHANGE post_update post_update DATETIME DEFAULT \'NULL\', CHANGE last_user_translation last_user_translation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rating CHANGE post_id post_id INT DEFAULT NULL, CHANGE entities_id entities_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE result DROP pos_year, CHANGE estrellas estrellas VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reintegros reintegros VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE complementos complementos VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE clave clave VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fraccion_serie fraccion_serie VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE caballo caballo VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE millon_joker millon_joker VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE bote bote VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fecha fecha DATE DEFAULT \'NULL\', CHANGE url_lectura url_lectura VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE section CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE title_es title_es VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE created_by created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_by updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token_logout_change_date token_logout_change_date INT DEFAULT NULL, CHANGE version version INT DEFAULT NULL');
        $this->addSql('ALTER TABLE view_counter CHANGE post_id post_id INT DEFAULT NULL');
    }
}
