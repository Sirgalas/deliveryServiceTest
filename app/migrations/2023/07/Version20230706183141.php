<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706183141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id UUID NOT NULL, city VARCHAR(255) DEFAULT NULL, village VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, value_type VARCHAR(255) DEFAULT \'street\' NOT NULL, home VARCHAR(255) DEFAULT NULL, building VARCHAR(255) DEFAULT NULL, flat SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE address IS \'Адресс\'');
        $this->addSql('COMMENT ON COLUMN address.id IS \'Идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN address.city IS \'Город.\'');
        $this->addSql('COMMENT ON COLUMN address.village IS \'Другой населеный пункт.\'');
        $this->addSql('COMMENT ON COLUMN address.street IS \'Улица.\'');
        $this->addSql('COMMENT ON COLUMN address.value_type IS \'Тип улицы.\'');
        $this->addSql('COMMENT ON COLUMN address.home IS \'Дом.\'');
        $this->addSql('COMMENT ON COLUMN address.building IS \'Корпус.\'');
        $this->addSql('COMMENT ON COLUMN address.flat IS \'Квартира.\'');
        $this->addSql('CREATE TABLE orders (id UUID NOT NULL, address_id UUID DEFAULT NULL, status VARCHAR(255) DEFAULT \'taken\' NOT NULL, date_start_order TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, date_delivery TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEEF5B7AF75 ON orders (address_id)');
        $this->addSql('COMMENT ON TABLE orders IS \'Заказы\'');
        $this->addSql('COMMENT ON COLUMN orders.id IS \'Идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN orders.address_id IS \'id таблицы Address(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN orders.status IS \'Статус.\'');
        $this->addSql('COMMENT ON COLUMN orders.date_start_order IS \'Дата поступления заказа\'');
        $this->addSql('COMMENT ON COLUMN orders.date_delivery IS \'Дата доставки\'');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEF5B7AF75');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE orders');
    }
}
