<?php

namespace App\Observers;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
{
    /**
     * @param  Expense  $expense
     */
    public function created(Expense $expense)
    {
        $this->setOrganization($expense);

        if ($expense->driver_id) {
            (new TransactionService())
                ->setDriver($expense->driver_id)
                ->setOrganization($expense->organization_id)
                ->setType(Transaction::TYPE_EXSPENCE)
                ->setActionID($expense->id)
                ->setActionBalance($expense->money)
                ->setDescription($expense->cargo_type ? $expense->cargo_type->name : null)
                ->create();
        }
    }

    public function saved(Expense $expense)
    {
        $this->setOrganization($expense);
    }

    protected function setOrganization(Expense $expense)
    {
        if (!$expense->driver_id) {
            $userOrganization = Driver::where([
                'is_organization' => true, 'organization_id' => $expense->organization_id,
            ])->first();
            $expense->driver_id = $userOrganization ? $userOrganization->id : null;

            try {
                $expense->save();
            } catch (\Exception $exception) {
                Log::error('error save expense model', ['msg' => $exception->getMessage()]);
            }
        }
    }
}
