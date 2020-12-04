<?php

use App\Currency;
use Illuminate\Database\Seeder;

class CurrenciesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $currencies=[
          'usd',
          'eru',
          'gbp',
      ];
      foreach($currencies as $currency){
          Currency::create([
            'iso'=>$currency,
          ]);
      }
    }
}
