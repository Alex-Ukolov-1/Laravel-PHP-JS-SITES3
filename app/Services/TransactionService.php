<?php

namespace App\Services;


use App\Models\Driver;
use App\Models\Transaction;
use App\Models\Trip;
use Carbon\Carbon;
use Auth;

/**
 * Class TransactionService
 * @package App\Services
 */
class TransactionService
{
    private $type;
    private $driver;
    private $organization;
    private $actionBalance;
    private $balance = null;
    private $description = null;
    private $actionId = null;
    private $note = null;

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param $organization
     *
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @param  null  $balance
     *
     * @return $this
     */
    public function setBalance($balance = null)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @param $driver
     *
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param  null  $description
     *
     * @return $this
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param $actionBalance
     *
     * @return $this
     */
    public function setActionBalance($actionBalance)
    {
        $this->actionBalance = $actionBalance;

        return $this;
    }

    /**
     * @param  null  $actionId
     *
     * @return $this
     */
    public function setActionID($actionId = null)
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * @param  null  $note
     *
     * @return $this
     */
    public function setNote($note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Transaction|false
     */
    public function create()
    {
        if ($this->driver) {
            try {
                $driver = Driver::findOrFail($this->driver);

                if ((int)$this->type === Transaction::TYPE_INCOME) {
                    $this->actionBalance = -1 * $this->actionBalance;
                }

                $balance = $this->balance ?: $driver->balance + $this->actionBalance;

                $transaction = Transaction::create([
                    'driver_id' => $driver->id,
                    'organization_id' => $this->organization,
                    'type' => $this->type,
                    'action_id' => $this->actionId,
                    'description' => $this->description,
                    'action_balance' => $this->actionBalance,
                    'balance' => $balance,
                    'date' => Carbon::now(),
                    'note' => $this->note,
                ]);

                $driver->balance = $balance;
                $driver->save();

                return $transaction;
            } catch (\Throwable $e) {

            }
        }

        return false;
    }

    /**
     * @param  Transaction  $transaction
     *
     * @return Transaction
     */
    public function recalcBalance(Transaction $transaction)
    {
        try {
            if ((int)$transaction->type === Transaction::TYPE_INCOME) {
                $transaction->action_balance = -1 * $transaction->action_balance;
            }

            $balance = $transaction->balance ?: $transaction->driver->balance + $transaction->action_balance;

            $transaction->balance = $balance;

            $transaction->driver->balance = $balance;
            $transaction->driver->save();

            return $transaction;
        } catch (\Throwable $e) {

        }
    }

    /**
     * @param  Trip  $trip
     */
    public function changeDriverSalaryInTrip(Trip $trip)
    {
        $totalDriverSalary = $trip->getOriginal('total_driver_salary') - $trip->total_driver_salary;

        $this->setDriver($trip->driver_id)
            ->setOrganization($trip->organization_id)
            ->setType(Transaction::TYPE_TRIP)
            ->setActionID($trip->id)
            ->setActionBalance($totalDriverSalary)
            ->setDescription($trip->cargo_type->name)
            ->setNote(
                sprintf('Изменение цены рейса из %s руб на %s руб, разница %s руб.',
                    $trip->getOriginal('total_driver_salary'),
                    $trip->total_driver_salary,
                    $totalDriverSalary)
            )
            ->create();
    }
}
