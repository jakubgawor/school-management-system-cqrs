<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241227233613 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table user (
    id char(36) not null,
    email varchar(64) not null,
    password varchar(255) not null,
    created_at datetime not null,
    is_verified boolean not null,
    primary key(id)
)
SQL
        );

        $this->addSql(
            <<<SQL
create index IDX_user_id on user (id)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
drop index IDX_user_id on user
SQL
        );

        $this->addSql(
            <<<SQL
drop table user
SQL
        );
    }
}
