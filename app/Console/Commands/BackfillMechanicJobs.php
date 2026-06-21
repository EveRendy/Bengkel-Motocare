<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use Illuminate\Console\Command;

class BackfillMechanicJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-mechanic-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill MechanicJob for ServiceOrders that have mechanic_id but no MechanicJob yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Backfilling MechanicJob...');

        $serviceOrders = ServiceOrder::whereNotNull('mechanic_id')
            ->whereDoesntHave('mechanicJob')
            ->get();

        $count = 0;
        foreach ($serviceOrders as $order) {
            \App\Models\MechanicJob::create([
                'service_order_id' => $order->id,
                'mechanic_id' => $order->mechanic_id,
                'started_at' => null,
                'finished_at' => null,
            ]);
            $count++;
            $this->line("Created MechanicJob for ServiceOrder #{$order->order_number}");
        }

        $this->info("Backfill complete! Created {$count} MechanicJob(s).");
    }
}
