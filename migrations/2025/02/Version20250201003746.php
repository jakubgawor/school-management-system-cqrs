<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250201003746 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table announcement (
    id char(36) not null,
    title varchar(256) not null,
    message varchar(4096) not null,
    created_at datetime not null,
    updated_at datetime null,
    primary key(id)
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
drop table announcement
SQL
        );
    }
}
