<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250114195758 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table subject (
    id char(36) not null,
    teacher_id char(36) not null,
    name varchar(255) not null,
    description varchar(4096) null,
    primary key(id)
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
drop table subject
SQL
        );
    }
}
