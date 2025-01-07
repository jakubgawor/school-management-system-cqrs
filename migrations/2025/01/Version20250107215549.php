<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250107215549 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table student (
    id char(36) not null,
    user_id char(36) not null,
    primary key(id)
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
drop table student
SQL
        );
    }
}
