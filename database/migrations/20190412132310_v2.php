<?php


use Phinx\Migration\AbstractMigration;

class V2 extends AbstractMigration
{
    public function change()
    {
        ($this->table('cb_videos', ['comment' => 'version:2']))
        ->addColumn('cover_id', 'string', ['null' => true])
        ->addColumn('duration', 'integer', ['null' => true])
        ->renameColumn('uri', 'url')
        ->update()
        ;
    }
}
