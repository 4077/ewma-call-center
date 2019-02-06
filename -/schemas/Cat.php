<?php namespace ewma\callCenter\schemas;

class Cat extends \Schema
{
    public $table = 'ewma_call_center_cats';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('position')->default(0);
            $table->string('name')->default('');
        };
    }
}
