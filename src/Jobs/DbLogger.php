<?php

namespace Telanflow\Sms\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DbLogger implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private $code,
        private $result,
        private $flag
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (!config('sms.dblog')) {
            return;
        }

        $tableName = config('sms.table_name', 'sms_log');
        DB::table($tableName)->insert([
            'mobile' => $this->code->to,
            'data' => json_encode($this->code),
            'is_sent' => $this->flag,
            'result' => $this->result,
            'modify_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'create_time' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
