<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220702141602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_couriers (id INT AUTO_INCREMENT NOT NULL, package_id INT NOT NULL, pickup_date DATE NOT NULL, pickup_time_from VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pickup_time_to VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_A38A03E1F44CABFF (package_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, shop_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, order_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, parcel_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_couriers ADD CONSTRAINT FK_A38A03E1F44CABFF FOREIGN KEY (package_id) REFERENCES packages (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE packages ADD order_courier_number VARCHAR(255) DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_couriers');
        $this->addSql('DROP TABLE orders');
        $this->addSql('ALTER TABLE packages DROP order_courier_number');
    }
}
