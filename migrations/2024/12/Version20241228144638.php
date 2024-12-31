<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241228144638 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
create table user_verification_token (
    id char(36) not null,
    user_id char(36) not null,
    token varchar(8) not null,
    created_at datetime not null,
    expires_at datetime not null,
    is_valid boolean not null,
    type varchar(255) not null,
    primary key(id),
    constraint FK_user_verification_token_user_id foreign key (user_id) references user (id) on delete cascade
)
SQL
        );

        $this->addSql(
            <<<SQL
create index IDX_user_verification_token_id on user_verification_token (id)
SQL
        );

        $this->addSql(
            <<<SQL
create index IDX_user_verification_token_user_id on user_verification_token (user_id)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
drop table user_verification_token
SQL
        );
    }
}
