<?php

use Illuminate\Database\Seeder;
use App\Models\Settings\Status;

class UpdateStatusesContracts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::query()->where('id', '=', 1)->update(['name' => 'Новый']);
        Status::query()->where('id', '=', 2)->update(['name' => 'Действующий']);
        Status::query()->where('id', '=', 3)->update(['name' => 'Закрыт']);
        Status::insert(['name' => 'Ожидает проверки', 'type_id' => 1]);
    }
}
