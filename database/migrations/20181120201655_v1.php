<?php


use Phinx\Migration\AbstractMigration;

class V1 extends AbstractMigration
{
    public function change()
    {
        ($this->table('cb_videos', ['comment' => 'version:1']))
        ->addColumn('source_id', 'string', ['null' => true])
        ->addColumn('title', 'string')
        ->addColumn('description', 'string', ['null' => true])
        ->addColumn('date_publication', 'datetime', ['null' => true])
        ->addColumn('type', 'string', ['limit' => 50])
        ->addColumn('uri', 'string')
        ->addColumn('status', 'integer', ['limit' => 1, 'signed' => false])
        ->create()
        ;
    }
}
