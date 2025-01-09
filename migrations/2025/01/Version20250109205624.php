<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250109205624 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table class_room add column created_at datetime not null
SQL
        );

        $this->addSql(
            <<<SQL
alter table class_room add column updated_at datetime null
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
alter table class_room drop column created_at
SQL
        );

        $this->addSql(
            <<<SQL
alter table class_room drop column updated_at
SQL
        );
    }
}
