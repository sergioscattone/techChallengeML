<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // add 10 random users
        for($i = 0; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name' => Str::random(10),
                'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('password'),
            ]);
        }
        // add 3 month data
        $months = [
            Carbon::now()->subMonth(2)->startOfMonth()->format('Y-m'),
            Carbon::now()->subMonth()->startOfMonth()->format('Y-m'),
            Carbon::now()->startOfMonth()->format('Y-m'),
        ];
        foreach($months as $month) {
            for($i = 0; $i <= 100; $i++) {
                $eventService = app('App\Services\EventService');
                $amount = rand(1, 100000)/100;
                $user = rand(1, 10);
                $currency = rand(1, 2);
                $type = rand(1, 8);
                $event = $eventService->create($amount, $user, $currency, $type);
                $dateTime = $this->getRandomDateTimeFromMonth($month);
                DB::update('update events set created_at = "'.$dateTime.'", updated_at = "'.$dateTime.'"  where id = ?', [$i]);
                DB::update('update charges set created_at = "'.$dateTime.'", updated_at = "'.$dateTime.'"  where id = ?', [$i]);
            }
            for($i = 0; $i <= 1000; $i++) {
                $paymentService = app('App\Services\PaymentService');
                $payAmount = rand(100000, 300000)/100;
                $payUser = rand(1, 10);
                $paymentService->createFromBulk($payAmount, $payUser);
            }
            $invoiceService = app('App\Services\InvoiceService');
            $invoiceService->consolidate($month);
        }
    }

    private function getRandomDateTimeFromMonth($month) {
        return $month.'-'.
            str_pad(rand(1, 28),2,0,STR_PAD_LEFT).' '.
            str_pad(rand(1, 23),2,0,STR_PAD_LEFT).':'.
            str_pad(rand(1, 59),2,0,STR_PAD_LEFT).':'.
            str_pad(rand(1, 59),2,0,STR_PAD_LEFT);
    }
}
