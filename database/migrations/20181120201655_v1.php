<?php


use Phinx\Migration\AbstractMigration;

class V1 extends AbstractMigration
{
    public function change()
    {
        ($this->table('videos', ['comment' => 'version:1']))
        ->addColumn('sourceId', 'string')
        ->addColumn('title', 'string')
        ->addColumn('description', 'string')
        ->addColumn('date_publication', 'datetime')
        ->addColumn('uri', 'string')
        ->addColumn('status', 'integer', ['limit' => 1])
        ->create()
        ;
    }
}
