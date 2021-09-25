<?php

namespace App\Models;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\FilterByRole;

class Transaction extends Model
{
    use CRUDFunctions;
    use FilterByRole;

    const TYPE_TRIP     = 1; // За рейс
    const TYPE_INCOME   = 2; // За доход
    const TYPE_EXSPENCE = 3; // За расход

    protected $table = 'transactions';

    protected $fillable = [
        'driver_id', 'organization_id', 'type', 'action_id', 'description', 'action_balance', 'balance', 'date', 'note',
    ];

    static public $types = [
        self::TYPE_TRIP     => 'За рейс',
        self::TYPE_INCOME   => 'За доход',
        self::TYPE_EXSPENCE => 'За расход'
    ];

    public static $model_name = 'Транзакции по балансу';

    protected static function booted()
    {
        static::creating(function ($transaction) {
            (new TransactionService())->recalcBalance($transaction);
        });
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function getTypeNameAttribute() {
        return self::$types[$this->type];
    }
}
