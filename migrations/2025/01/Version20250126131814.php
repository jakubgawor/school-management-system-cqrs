<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250126131814 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table grade (
    id char(36) not null,
    teacher_id char(36) not null,
    student_id char(36) not null,
    subject_id char(36) not null,
    grade varchar(64) not null,
    weight integer(8) not null,
    description varchar(4096) not null,
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
drop table grade
SQL
        );
    }
}
