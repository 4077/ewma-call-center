<?php namespace ewma\callCenter\schemas;

class Call extends \Schema
{
    public $table = 'ewma_call_center_calls';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('cat_id')->default(0);
            $table->integer('position')->default(0);
            $table->string('name')->default('');
            $table->string('path')->default('');
            $table->longText('data');
            $table->longText('inputs');
            $table->boolean('require_confirmation')->default(false);
            $table->boolean('cron_enabled')->default(false);
            $table->string('cron_schedule')->default('* * * * *');
            $table->boolean('async_enabled')->default(false);
            $table->integer('async_ttl')->default(0);
            $table->string('async_queue')->default('default');
            $table->integer('async_priority')->default(0);
            $table->boolean('envs_filter_enabled')->default(false);
            $table->string('envs_filter')->default('');
            $table->longText('last_output');
        };
    }
}
