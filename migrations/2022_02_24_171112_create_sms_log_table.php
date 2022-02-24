<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaravelSmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('sms.table_name', 'sms_log');
        if (!Schema::hasTable($tableName))
        {
            Schema::create($tableName, function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('mobile')->nullable(false)->comment('手机号');
                $table->text('data')->nullable();
                $table->tinyInteger('is_sent')->default(0);
                $table->text('result')->nullable();
                $table->tinyInteger('is_delete')->nullable(false)->default(0)->comment('是否删除 1是 0否');
                $table->dateTime('modify_time')->nullable(false)->useCurrentOnUpdate()->useCurrent()->comment('修改时间');
                $table->dateTime('create_time')->nullable(false)->useCurrent()->comment('创建时间');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = config('sms.table_name', 'sms_log');
        Schema::dropIfExists($tableName);
    }
}
