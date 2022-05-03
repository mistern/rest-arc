<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220503092807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial Archiving Stats table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE archiving_stats (
                    date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', 
                    ip_address VARCHAR(15) NOT NULL, 
                    count INT NOT NULL, 
                    PRIMARY KEY(date, ip_address)) 
                    DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` 
                    ENGINE = InnoDB
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE archiving_stats');
    }
}
