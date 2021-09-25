<?php

namespace App\Observers;

use App\Models\Driver;
use App\Models\Income;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;

class IncomeObserver
{
    /**
     * Handle the income "creating" event.
     *
     * @param  Income  $income
     *
     * @return void
     */
    public function created(Income $income)
    {
        $this->setOrganization($income);

        if ($income->driver_id) {
            (new TransactionService())
                ->setDriver($income->driver_id)
                ->setOrganization($income->organization_id)
                ->setType(Transaction::TYPE_INCOME)
                ->setActionID($income->id)
                ->setActionBalance($income->money)
                ->setDescription($income->cargo_type ? $income->cargo_type->name : null)
                ->create();
        }
    }

    public function saved(Income $income) {
        $this->setOrganization($income);
    }

    /**
     * @param  Income  $income
     */
    protected function setOrganization(Income $income)
    {
        if (!$income->driver_id) {
            $userOrganization = Driver::where([
                'is_organization' => true, 'organization_id' => $income->organization_id,
            ])->first();
            $income->driver_id = $userOrganization ? $userOrganization->id : null;

            try {
                $income->save();
            } catch (\Exception $exception) {
                Log::error('error save income model', ['msg' => $exception->getMessage()]);
            }
        }
    }
}
