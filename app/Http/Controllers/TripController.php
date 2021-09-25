<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ExportController as Export;
use App\Models\Trip;
use App\Models\Car;
use App\Models\Organization;
use App\Models\TripDocument;
use App\Models\Driver;
use App\Models\Contract;
use App\Models\Counterparty;
use App\Models\Settings\DeparturePoint;
use App\Models\Settings\Destination;
use App\Models\Settings\IntermediatePoint;
use App\Models\Settings\StopAndService;
use App\Models\Settings\CargoType;
use App\Models\Settings\UnitType;
use App\Models\Settings\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Auth;

class TripController extends CRUDController
{
    protected $model = Trip::class;

    protected $route = 'trips';

    protected $title = 'Рейсы';

    protected $sort = self::SORT_BY_DATE;

    protected $row_buttons = ['edit', 'delete'];
    protected $form_buttons = ['save', 'save_and_to_duplicate', 'save_and_exit', 'cancel'];

    protected $datatable_plugins = ['export_trips'];

    protected $fields = [
        [
            'name' => 'id',
            'data' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'strict_search' => true,
            'skipInTable' => true,
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'organization_id',
            'data' => 'organization->name',
            'title' => 'Организация',
            'type' => 'select',
            'source' => Organization::class,
            'required' => true,
            'forAdminOnly' => true,
        ],
        [
            'title' => 'Информация о рейсе',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'date',
            'data' => 'date_only',
            'title' => 'Дата',
            'type' => 'date',
            'required' => true,
        ],
        [
            'name' => 'driver_id',
            'data' => 'driver->name',
            'title' => 'Водитель',
            'type' => 'select',
            'source' => Driver::class,
            'onchange' => 'selectCarAndContract(this)',
            'required' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'driver_id',
            'data' => 'driver->name',
            'title' => 'Водитель',
            'type' => 'select',
            'source' => Driver::class,
            'required' => true,
            'skipInTable' => true,
            'skipInCreate' => true,
            'skipInShow' => true,
            'skipInExport' => true,
        ],
        [
            'name' => 'car_id',
            'data' => 'car->number',
            'title' => 'Машина',
            'type' => 'select',
            'source' => Car::class,
            'required' => true,
        ],
        [
            'name' => 'contract_id',
            'data' => 'contract->name_for_list',
            'title' => 'Заказ',
            'type' => 'select',
            'source' => Contract::class,
            'onchange' => "fillBy(this, 'contracts', ['date'])",
            'skipForDriver' => true,
        ],
        [
            'name' => 'cargo_type_id',
            'data' => 'cargo_type->name',
            'title' => 'Груз',
            'type' => 'select',
            'source' => CargoType::class,
            'required' => true,
        ],
        [
            'name' => 'departure_point_id',
            'data' => 'departure_point->name',
            'title' => 'Пункт погрузки',
            'type' => 'select',
            'source' => DeparturePoint::class,
            'required' => true,
        ],
        [
            'name' => 'intermediate_point',
            'data' => 'intermediate_point_names',
            'title' => 'Промежуточные пункты',
            'type' => 'multiselect-labels',
            'source' => IntermediatePoint::class,
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'stop_and_service',
            'data' => 'stop_and_service_names',
            'title' => 'Стоянки и сервисы',
            'type' => 'multiselect-labels',
            'source' => StopAndService::class,
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        //-----------------
        [
            'name' => 'loading_cargo_amount',
            'data' => 'loading_cargo_amount',
            'title' => 'Кол-во на погрузке',
            'type' => 'float',
            'required' => true,
            'with_total' => true,
        ],
        [
            'name' => 'loading_unit_type_id',
            'data' => 'loading_unit_type->name',
            'title' => 'Погрузка - Ед.изм.',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
        ],
        //
        [
            'name' => 'destination_id',
            'data' => 'destination->name',
            'title' => 'Пункт разгрузки',
            'type' => 'select',
            'source' => Destination::class,
            'required' => true,
        ],
        //------------------------
        
        [
            'name' => 'unloading_cargo_amount',
            'data' => 'unloading_cargo_amount',
            'title' => 'Кол-во на разгрузке',
            'type' => 'float',
            'required' => true,
            'with_total' => true,
        ],
        [
            'name' => 'unloading_unit_type_id',
            'data' => 'unloading_unit_type->name',
            'title' => 'Разгрузка - Ед.изм.',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
        ],
        //
        [
            'title' => 'Справочная информация',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Примечание',
            'type' => 'textarea',
            'skipInTable' => true,
        ],
        [
            'name'         => 'files',
            'title'        => 'Документы',
            'source'       => TripDocument::class,
            'type'         => 'file_table',
            'skipInTable'  => true,
            'skipInShow'   => true,
        ],
        [
            'name'          => 'revenue',
            'data'          => 'revenue',
            'title'         => 'Общая выручка',
            'skipInCreate'  => true,
            'skipInEdit'    => true,
            'skipForDriver' => true,
            'type'          => 'float',
        ],
        [
            'name'          => 'profit',
            'data'          => 'profit',
            'title'         => 'Прибыль за рейс',
            'skipInCreate'  => true,
            'skipInEdit'    => true,
            'skipForDriver' => true,
            'type'          => 'float',
        ],
        [
            'name'          => 'total_driver_salary',
            'data'          => 'total_driver_salary',
            'title'         => 'З/П Водителя',
            'skipForDriver' => true,
            'type'          => 'float',
        ],
        [
            'name'          => 'contract->unloading_payment_type->id',
            'data'          => 'contract->unloading_payment_type->name',
            'title'         => 'Форма оплаты',
            'type'          => 'select',
            'source'        => PaymentType::class,
            'skipInCreate'  => true,
            'skipInEdit'    => true,
            'skipForDriver' => true,
        ]
    ];

    protected $driver_fields = [
        [
            'title' => 'Информация о рейсе',
            'type' => 'subtitle',
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'date',
            'data' => 'date_only',
            'title' => 'Дата',
            'type' => 'date',
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'car_id',
            'data' => 'car->number',
            'title' => 'Машина',
            'type' => 'select',
            'source' => Car::class,
            'required' => true,
        ],
        [
            'name' => 'contract_id',
            'data' => 'contract->name_for_list',
            'title' => 'Заказ',
            'type' => 'select',
            'source' => Contract::class,
            'onchange' => "fillBy(this, 'contracts', ['date'])",
            'skipInTable' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'cargo_type_id',
            'data' => 'cargo_type->name',
            'title' => 'Груз',
            'type' => 'select',
            'source' => CargoType::class,
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'departure_point_id',
            'data' => 'departure_point->name',
            'title' => 'Пункт погрузки',
            'type' => 'select',
            'source' => DeparturePoint::class,
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'intermediate_point',
            'data' => 'intermediate_point_names',
            'title' => 'Промежуточные пункты',
            'type' => 'multiselect-labels',
            'source' => IntermediatePoint::class,
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'stop_and_service',
            'data' => 'stop_and_service_names',
            'title' => 'Стоянки и сервисы',
            'type' => 'multiselect-labels',
            'source' => StopAndService::class,
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'loading_unit_type_id',
            'data' => 'loading_unit_type->name',
            'title' => 'Погрузка - Ед.изм. груза',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'loading_cargo_amount',
            'data' => 'loading_cargo_amount',
            'title' => 'Погрузка - Кол. груза',
            'type' => 'float',
            'required' => true,
            'with_total' => true,
        ],
        [
            'name' => 'destination_id',
            'data' => 'destination->name',
            'title' => 'Пункт разгрузки',
            'type' => 'select',
            'source' => Destination::class,
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'unloading_unit_type_id',
            'data' => 'unloading_unit_type->name',
            'title' => 'Разгрузка - Ед.изм. груза',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'unloading_cargo_amount',
            'data' => 'unloading_cargo_amount',
            'title' => 'Разгрузка - Кол. груза',
            'type' => 'float',
            'required' => true,
            'with_total' => true,
        ],
        [
            'title' => 'Справочная информация',
            'type' => 'subtitle',
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Примечание',
            'type' => 'textarea',
            'skipInTable' => true,
            'skipInCreate' => true,
        ],
        [
            'name'         => 'files',
            'title'        => 'Документы',
            'source'       => TripDocument::class,
            'type'         => 'file_table',
            'skipInTable'  => true,
            'skipInCreate' => true,
            'skipInShow'   => true,
        ],
    ];

    public $auto_load_relations = ['organization', 'driver', 'car', 'contract', 'cargo_type', 'departure_point', 'loading_unit_type', 'destination', 'unloading_unit_type', 'contract.unloading_payment_type'];

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (Auth::guard('driver')->check()) {
                $this->fields = $this->driver_fields;
            }

            return $next($request);
        });

        parent::__construct();
    }

    public function index()
    {
        if (\request()->query->has('counterparty_id')) {
            $counterparty_id = \request()->query->get('counterparty_id');
            $counterparty = Counterparty::find($counterparty_id);

            Contract::addGlobalScope('counterparty', function($query) use ($counterparty) {
                $query->where($counterparty->type_name . '_id', $counterparty->id);
            });

            $this->title = $this->title . ' - ' . $counterparty->name;
        }

        return parent::index();
    }

    public function applyCustomFilters(&$fields, &$query) {
        if (\request()->query->has('counterparty_id')) {
            $counterparty_id = \request()->query->get('counterparty_id');
            $counterparty = Counterparty::find($counterparty_id);

            $query->whereHas('contract', function($query) use ($counterparty) {
                $query->where($counterparty->type_name . '_id', $counterparty->id);
            });
        }
    }

    public function create( string $custom_view = null, array $additional_data = [] )
    {
        if (Auth::guard('driver')->check()) {
            $driver = Auth::guard('driver')->user();

            $contract = $driver->contract;
            $car = $driver->car;

            if (empty($contract) || empty($car)) {
                return parent::create('trips.warning_driver', [
                    'contract_exists' => !empty($contract),
                    'car_exists' => !empty($car),
                ]);
            } else {
                $contracts = Contract::where('status_id', '!=', 3)->get();
                $cars      = Car::paid()->where('status', 1)->get();

                return parent::create('trips.create_driver', [
                    'contracts' => $contracts,
                    'cars'      => $cars,
                    'current_contract_id' => $contract->id,
                    'current_car_id' => $car->id,
                ]);
            }
        } else {
            return parent::create();
        }
    }

    public function store(Request $request)
    {
        $id = DB::select("SHOW TABLE STATUS LIKE 'trips'");
        $id_of_next_trip = $id[0]->Auto_increment;

        $waybills = $request->file('waybills');

        if ($waybills) {
            foreach ($waybills as $waybill){
                $fileName = "Накладная рейса " . $id_of_next_trip . '.' . $waybill->extension();
                $waybill->move(public_path('uploads'), $fileName);
                $TripDocument                       = new TripDocument();
                $TripDocument->trip_id              = $id_of_next_trip;
                $TripDocument->trip_document_number = 1;
                $TripDocument->document_path        = $fileName;
                $TripDocument->document_comment     = "Накладная рейса " . $id_of_next_trip;
                $TripDocument->document_type_id     = 1;
                $TripDocument->save();
            }
        }

        if (Auth::guard('driver')->check()) {

            $driver = Auth::guard('driver')->user();

            if (empty($driver->contract) || empty($driver->car)) {
                abort(403);
            }

            // Водитель создаёт новый заказ
            if (!empty($request->get('cargo_type_id'))) {
                $organization  = $driver->organization;
                $driver_salary = $request->get('distance') * $organization->price_per_km;

                $contract = Contract::create([
                    'organization_id' => $organization->id,
                    'number' => 'number '.date('Y-m-d H:i:s'),
                    'name'   => 'name '.date('Y-m-d H:i:s'),
                    'date'   => date('Y-m-d'),
                    'cargo_type_id' => $request->get('cargo_type_id'),
                    'distance'      => $request->get('distance'),
                    'conversion_factor'   => 1.00,
                    'departure_point_id'  => $request->get('departure_point_id'),
                    'destination_id'    => $request->get('destination_id'),
                    'vat_in_fuel_expenses' => $organization->vat_in_fuel_expenses,
                    'distance_price'    => $organization->price_per_km,
                    'status_id'         => 4,
                    'trip_direction_id' => 1,
                    'driver_salary'     => $driver_salary,
                    'driver_salary_type_id'  => 1,
                    'unloading_unit_type_id' => 1,
                    'loading_unit_type_id'  => 1,
                    'unloading_price'       => 0,
                    'vat_in_income'         => 0,
                    'vat_in_cargo_expenses' => 0,
                    'unloading_payment_type_id' => 1,
                ]);
            } else {
                $contract = Contract::findOrFail($request->get('contract_id'));
            }

            $this->request_data = array_merge($this->request_data, [
                'date' => date('Y-m-d H:i:s'),
                'contract_id' => $contract->id,
                'cargo_type_id' => $contract->cargo_type_id,
                'departure_point_id' => $contract->departure_point_id,
                'destination_id' => $contract->destination_id,
                'loading_unit_type_id' => $contract->loading_unit_type_id,
                'unloading_unit_type_id' => $contract->unloading_unit_type_id,
            ]);
        }

        $response = parent::store($request);

        if (Auth::guard('driver')->check()) {
            $this->item->driver->changeCar($this->item->car->id);
            $this->item->driver->changeContract($this->item->contract->id);
        }

        return $response;
    }

    public function update(Request $request, $id)
    {
        if (!empty($request->get('trip_files'))) {

            $request_trip_files_data = $request->get('trip_files');
            $request_trip_files_groups = $request->file('trip_files');

            if (!empty($request_trip_files_groups)) {
                foreach ($request_trip_files_groups as $key => $request_trip_files_group) {
                    foreach ($request_trip_files_group as $request_trip_file) {
                        $trip_document = TripDocument::query()
                            ->where('trip_id', '=', $id)
                            ->where('trip_document_number', '=', $request_trip_files_data[$key]['file_number'])
                            ->first();

                        if (empty($trip_document)) {
                            $this->saveTripDocument($request_trip_file, $request_trip_files_data[$key], $id);
                        } else {
                            $this->updateTripDocument($trip_document, $request_trip_files_data[$key], $request_trip_file);
                        }
                    }
                }
            } else {
                foreach ($request_trip_files_data as $key => $request_trip_file_data) {
                    $trip_document = TripDocument::query()
                        ->where('trip_id', '=', $id)
                        ->where('trip_document_number', '=', $request_trip_file_data['file_number'])
                        ->first();

                    if ($trip_document) {
                        $this->updateTripDocument($trip_document, $request_trip_file_data);
                    }
                }
            }
        }

        return parent::update($request, $id);
    }

    public function destroy($id) {
        if (Auth::guard('driver')->check()) {
            abort(403);
        }

        return parent::destroy($id);
    }

    protected function saveTripDocument(UploadedFile $trip_file, Array $trip_file_data, $trip_id) {
        $fileName = $this->generateTripDocumentFileName($trip_id, $trip_file_data['document_type'], $trip_file->extension());

        $trip_file->move(public_path('uploads'), $fileName);

        $TripDocument                       = new TripDocument();
        $TripDocument->trip_id              = $trip_id;
        $TripDocument->trip_document_number = $trip_file_data['file_number'];
        $TripDocument->document_path        = $fileName;
        $TripDocument->document_comment     = $trip_file_data['file_comment'];
        $TripDocument->document_type_id     = $trip_file_data['document_type'];
        $TripDocument->save();
    }

    protected function updateTripDocument($tripDocument, $request_trip_document_data, UploadedFile $trip_file = null) {
        $current_trip_file = new File(public_path('uploads/') . $tripDocument->document_path);
        $file_extension = is_null($trip_file) ? $current_trip_file->extension() : $trip_file->extension();
        $fileName = $this->generateTripDocumentFileName($tripDocument->trip_id, $request_trip_document_data['document_type'], $file_extension);

        $tripDocument->document_comment = $request_trip_document_data['file_comment'];

        if ($tripDocument->document_type_id !== $request_trip_document_data['document_type'] && is_null($trip_file)){
            $tripDocument->document_type_id = $request_trip_document_data['document_type'];
            $tripDocument->document_path = $fileName;
            $current_trip_file->move(public_path('uploads'), $fileName);
        }

        if (!is_null($trip_file) && $trip_file->getSize() !== $current_trip_file->getSize()) {
            $trip_file->move(public_path('uploads'), $fileName);
            //TODO: удалить старый файл
        }

        $tripDocument->save();
    }

    protected function generateTripDocumentFileName($trip_id, $trip_document_type, $extension): string {
        $document_type = "";

        switch ($trip_document_type){
            case 1:
                $document_type = "ТТН";
                break;
            case 2:
                $document_type = "ТРН";
                break;
            case 3:
                $document_type = "ТН";
                break;
            case 4:
                $document_type = "АКТ";
                break;
            case 5:
                $document_type = "Д";
                break;
            default:
                $document_type = "Д";
        }

        $trip = Trip::find($trip_id);

        if (!isset($trip->contract->code)) {
            return $document_type."_".$trip->date."_".$trip->car->number."_".$trip->driver->name.".".$extension;
        } else {
            return $document_type."_".$trip->date."_".$trip->contract->code."_".$trip->car->number."_".$trip->driver->name.".".$extension;
        }


    }

    protected function exportToXls(Request $request) {
        $this->getFiltered($request);

        $query_with_filters = $this->filtered_query_without_pagination;

        $data       = $this->prepareExportData(clone $query_with_filters);
        $max_date   = $this->getMaxDateRange(clone $query_with_filters);
        $min_date   = $this->getMinDateRange(clone $query_with_filters);
        $date_range = $min_date . " - " . $max_date;
        $filename   = $this->getExportFileName('xls', $date_range);

        $filename = mb_convert_encoding($filename, 'utf-8');

        return Excel::download(new Export($data, 'trip_xls'), $filename);
    }

    private function prepareExportData($query) {
        $data       = $this->getExportData($query);
        $rows       = [];
        $header     = [];
        $header[0]  = '';
        $rows[]     = $header;
        $row_number = 1;

        foreach ($data['data'] as $row => $data) {
            foreach ($data as $column => $value) {
                $rows[$row_number][$column] = $value;
            }

            $row_number++;
        }

        return $rows;
    }

    public function getExportData($query) {
        ini_set('max_execution_time', '1200');
        ini_set('memory_limit', '1536M');

        $data = [];

        $trips = $query->get();

        $data['data'] = [];
        $data['data'][] = [
            '',
            'Дата',
            'Груз',
            'Пункт разгрузки',
            'Кол-во груза на разгр.',
            'Ед. изм. груза на разгр.',
            'Номер машины',
            'Водитель',
            'Код договора',
            'Номер договора',
            'Название',
            'Цена за ед. груза, руб.',
            'Цена за весь груз, руб.',
            'Примечание',
        ];

        foreach ($trips as $key => $trip) {
            $data['data'][] = [
                $key + 1,
                $trip->date                      ? date('Y-m-d', strtotime($trip->date)) : '',
                $trip->cargo_type->name          ?? '',
                $trip->destination->name         ?? '',
                $trip->unloading_cargo_amount    ?? '',
                $trip->unloading_unit_type->name ?? '',
                $trip->car->number               ?? '',
                $trip->driver->name              ?? '-',
                $trip->contract->code            ?? '',
                $trip->contract->number          ?? '',
                $trip->contract->name            ?? '',
                $trip->contract->unloading_price ?? '',
                (!empty($trip->contract->unloading_price) && !empty($trip->unloading_cargo_amount)) ? $trip->contract->unloading_price * $trip->unloading_cargo_amount : '',
                $trip->comment                   ?? '',
            ];
        }

        return $data;
    }

    private function getExportFileName($extension, $date_range) {
        $filename = "Рейсы " . $date_range.".".$extension;
        return $filename;
    }

    private function getMinDateRange($query) {
        $min_date = $query->min('date');

        return date('Y-m-d', strtotime($min_date));
    }

    private function getMaxDateRange($query){
        $max_date = $query->max('date');

        return date('Y-m-d', strtotime($max_date));
    }

    public function getArchiveDocs($id) {
        $trip = Trip::findOrFail($id);

        $zip = new \ZipArchive();
        $filename = 'Документы рейса №'.$trip->id.".zip";
        $filepath = public_path('uploads/' . $filename);

        if ($zip->open($filepath, \ZipArchive::CREATE) !== TRUE) {
            exit("Невозможно открыть <$filepath>\n");
        }

        foreach ($trip->documents as $document) {
            try {
                $path_doc = public_path('uploads/' . $document->document_path);
                $zip->addFile($path_doc, $document->document_path);
            } catch (\Exception $exception) {}

        }

        $zip->close();

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"".rawurlencode($filename)."\"");
        header("Content-Length: " . filesize($filepath));

        readfile($filepath);
        unlink($filepath);
    }

    public function getArchiveAllDocs(Request $request) {
        ini_set('max_execution_time', '1200');
        ini_set('memory_limit', '1536M');

        $this->getFiltered($request);

        $query_with_filters = $this->filtered_query_without_pagination;

        $max_date = $this->getMaxDateRange(clone $query_with_filters);
        $min_date = $this->getMinDateRange(clone $query_with_filters);

        $date_range = $min_date . " - " . $max_date;

        $trips = $this->filtered_query_without_pagination->get();

        $zip = new \ZipArchive();
        $filename = 'Документы рейсов ' . $date_range . '.zip';

        $filepath = public_path('uploads/'.$filename);

        if ($zip->open($filepath, \ZipArchive::CREATE) !== TRUE) {
            exit("Невозможно открыть <$filepath>\n");
        }

        foreach ($trips as $trip) {
            foreach ($trip->documents as $document) {
                try {
                    $path_doc = public_path('uploads/' . $document->document_path);
                    $zip->addFile($path_doc, $document->document_path);
                } catch (\Exception $exception) {}
            }
        }

        $zip->close();

        if (is_file($filepath) ){
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"".rawurlencode($filename)."\"");
            header("Content-Length: " . filesize($filepath));

            readfile($filepath);
            unlink($filepath);
        } else {
             return ['error' => 'Документы не найдены!'];
        }
    }

    public function exportToEmailXls(Request $request) {
        return $this->exportToEmail($request, 'xls');
    }

    public function exportToEmailAll(Request $request) {
        return $this->exportToEmail($request, 'all');
    }

    public function exportToEmail(Request $request, string $export_type) {
        ini_set('max_execution_time', '1200');
        ini_set('memory_limit', '1536M');

        $email = $request->get('email');

        $this->getFiltered($request);

        $query_with_filters = $this->filtered_query_without_pagination;

        $data       = $this->prepareExportData(clone $query_with_filters);
        $max_date   = $this->getMaxDateRange(clone $query_with_filters);
        $min_date   = $this->getMinDateRange(clone $query_with_filters);
        $date_range = $min_date . " - " . $max_date;
        $excel_filename = $this->getExportFileName('xls', $date_range);

        Excel::store(new Export($data,'trip_xls'), $excel_filename);

        if ($export_type === 'all') {
            $zip = new \ZipArchive();
            $filename = 'Документы рейсы ' . $date_range . ".zip";

            $filepath = public_path('uploads/' . $filename);

            if ($zip->open($filepath, \ZipArchive::CREATE) !== TRUE) {
                exit("Невозможно открыть <$filepath>\n");
            }

            $path_doc = storage_path("app/" . $excel_filename);
            $zip->addFile($path_doc, basename($excel_filename));

            $trips = $query_with_filters->get();

            foreach ($trips as $trip) {
                foreach ($trip->documents as $document) {
                    try {
                        $path_doc = public_path('uploads/' . $document->document_path);
                        $zip->addFile($path_doc, $document->document_path);
                    } catch (\Exception $exception) {}
                }
            }

            $zip->close();

            $subject = "Документы, рейсы " . $date_range;
        } else {
            $filepath = storage_path("app/" . $excel_filename);
            $subject  = "Реестр рейсов " . $date_range;
        }

        if (Auth::guard('organization')->check()) {
            $organization = Auth::guard('organization')->user();
        } else if (Auth::guard('driver')->check()) {
            $organization = Auth::guard('driver')->user()->organization;
        }

        if (!empty($organization)) {
            $subject .= " компании " . $organization->name;
        }

        if (is_file($filepath) ){
            $filename = basename($filepath);
            $file = fopen($filepath, 'r');

            Mail::send('emails.sendtrips', [], function($message) use($file, $filename, $email, $subject) {
                $message->to($email)->subject($subject);
                $message->attachData(stream_get_contents($file), $filename);
            });

            fclose($file);
            unlink($filepath);

            return ['success' => 'Документы отправлены!'];
        } else {
            return ['error' => 'Документы не найдены!'];
        }
    }

}
