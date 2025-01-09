<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250109192652 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table student add column class_room_id char(36) null
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table student drop column class_room_id
SQL
        );
    }
}
