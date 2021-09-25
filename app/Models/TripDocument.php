<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;

class TripDocument extends Model
{
  protected $table = 'trip_documents';

	use CRUDFunctions;

	protected $fillable = [
	  'trip_id', 'trip_document_number', 'document_path', 'document_comment', 'document_type_id',
	];

	public $timestamps = false;

  public function trip() {
    return $this->hasOne('App\Models\Trip', 'id', 'trip_id');
  }
}
