<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250106195741 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table user 
    add first_name varchar(64) not null,
    add last_name varchar(64) not null
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table user 
    drop first_name, 
    drop last_name
SQL
        );
    }
}
