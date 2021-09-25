<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Auth;

class CRUDController extends Controller
{
    const SORT_DEFAULT = ['field' => 'id', 'order' => 'DESC'];
    const SORT_BY_DATE = ['field' => 'date', 'order' => 'DESC'];

    protected $model;

    protected $route;

    protected $title = 'Title';

    protected $sort = self::SORT_DEFAULT;

    protected $top_buttons = ['add', 'delete', 'select_all', 'deselect_all'];
    protected $row_buttons = ['edit', 'delete'];
    protected $form_buttons = ['save', 'save_and_exit', 'cancel'];
    protected $form_buttons_driver = ['save', 'cancel'];

    protected $append_js = [];
    protected $datatable_plugins = [];

    private $fieldsAttributes = [
        'name', //
        'data', //
        'title', //
        'type', //
        'strict_search', //
        'source', // Используется для списков. Указывается класс, из которого брать объекты для списка
        'scope', // Используется для списков. Указывается scope модели, если в списке требуется вывести не все записи из базы, а выбранные по определённому критерию (определённый scope)
        'required', // Сделать поле обязательным для заполнения
        'with_total', // Вывести под таблицей сумму значений данного поля (то есть сумму всех значений данной колонки таблицы)
        'skipInTable', // Не выводить поле в таблице
        'skipInCreate', // Не выводить поле на странице создания
        'skipInEdit', // Не выводить поле на странице редактирования
        'skipInShow', // Не выводить поле в просмотре записи
        'skipInExport', //
        'forAdminOnly', // Отображать данное поле только Администраторам
        'forOrganizationOnly', // Отображать данное поле только Организациям
        'forDriverOnly', // Отображать данное поле только Водителям
        'skipForDriver', // Не показывать поле Водителям
        'skipForOrganization', // Не показывать поле Организациям
        'skipForAdmin', // Не показывать поле Администраторам
        'dataPreprocessing', //
        'view', //
        'default_value', //
        'onchange', //
        'boolean_turn_on',
        'boolean_turn_off',
        'value_prepend', //
        'value_append', //
        'relation', //
        'class', // Судя по всему, ни где не используется
        'width', // Судя по всему, ни где не используется
    ];

    protected $fields = [];
    protected $query;
    protected $filtered_query_without_pagination;

    protected $auto_load_relations = ['organization'];

    protected $tableFields = [];
    protected $createFields = [];
    protected $editFields = [];
    protected $showFields = [];
    protected $exportFields = [];

    protected $createValidate = [];
    protected $editValidate = [];

    protected $request_data = [];

    public function __construct() {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->init();

            return $next($request);
        });
    }

    public function init() {
        $this->model = new $this->model;

        // Заполняем всем полям недостающие атрибуты, чтобы не возникало ошибок, если мы где-то обратимся к несуществующему атрибуту
        $this->prepareFields($this->fields);

        $this->filterFields();

        $this->prepareValidationRules();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('crud.index', [
            'content' => $this->datatable(),
            'title'   => $this->title
        ]);
    }

    public function datatable() {
        return view('crud.datatable', [
            'fields' => $this->tableFields,
            'title' => $this->title,
            'route' => $this->route,
            'sort' => (object)$this->sort,
            'top_buttons' => $this->top_buttons,
            'plugins' => $this->datatable_plugins,
        ]);
    }

    // Заполняет полям недостающие атрибуты, чтобы не возникало ошибок, если мы где-то обратимся к несуществующему атрибуту
    public function prepareFields(array &$fields) {
        foreach ($fields as &$field) {
            foreach ($this->fieldsAttributes as $attribute) {
                if (!isset($field[$attribute])) $field[$attribute] = null;
            }
        }
    }


    public function filterFields() {
        /*
         * Выясняем тип пользователя
        */

        $user_is_admin = Auth::guard('admin')->check();
        $user_is_driver = Auth::guard('driver')->check();
        $user_is_organization = Auth::guard('organization')->check();

        foreach ($this->fields as $index => $field) {
            $name = $field['name'] ?? $index;

            // Не показываем поле "id" для Организаций и Водителей
            if (($user_is_organization || $user_is_driver) && $field['name'] === 'id') continue;

            // Не показываем поле "Организация" для Организаций и Водителей
            if (($user_is_organization || $user_is_driver) && $field['name'] === 'organization_id') continue;

            // Не показываем поле "Водитель" для Водителей
            if ($user_is_driver && $field['name'] === 'driver_id') continue;

            /*
             * Фильтруем поля по типу пользователя
            */

            if ($field['forAdminOnly'] === true && $user_is_admin === false) continue;
            if ($field['forOrganizationOnly'] === true && $user_is_organization === false) continue;
            if ($field['forDriverOnly'] === true && $user_is_driver === false) continue;

            if ($field['skipForDriver'] === true && $user_is_driver === true) continue;
            if ($field['skipForOrganization'] === true && $user_is_organization === true) continue;
            if ($field['skipForAdmin'] === true && $user_is_admin === true) continue;

            /*
             * Сортируем поля по отдельным массивам для различных разделов (таблица с данными, редактирование, добавление, экспорт)
            */

            if ($field['skipInTable'] !== true) $this->tableFields[$name] = $field;
            if ($field['skipInCreate'] !== true) $this->createFields[$name] = $field;
            if ($field['skipInEdit'] !== true) $this->editFields[$name] = $field;
            if ($field['skipInShow'] !== true) $this->showFields[$name] = $field;
            if ($field['skipInExport'] !== true) $this->exportFields[$name] = $field;
        }
    }

    // Подготавливает список правил для валидаций при создании (store) или обновлении (update) записи
    public function prepareValidationRules() {
        // Формируем массив правил для валидации полей при создании новой записи
        foreach ($this->createFields as $field) {
            if ($field['required'] === true) $this->createValidate[$field['name']] = 'required';
        }

        // Формируем массив правил для валидации полей при редактировании записи
        foreach ($this->editFields as $field) {
            if ($field['required'] === true) $this->editValidate[$field['name']] = 'required';
        }
    }

    public function list(Request $request, $main_field_name = 'name') {
        return \response()->json(
            $this->model->select(['id', "$main_field_name as text"])
                ->where($main_field_name, 'like', '%'.$request->input('q').'%')
                ->applyScopes()->getQuery()->get()
        );
    }

    private function search(&$query, $column_name, $search) {
        $value = $search['value'];
        $strict = $search['strict'];

        if (!empty($search['type']) && $search['type'] === 'date' && gettype($value) === 'array') {
            if (!empty($value[0])) $query->whereDate($column_name, '>=', $value[0]);
            if (!empty($value[1])) $query->whereDate($column_name, '<=', $value[1]);

            return;
        }

        if (gettype($value) === 'array') {
            $query->whereIn($column_name, $value);
        } else {
            if ($strict === false) {
                $query->where($column_name, 'like', '%' . $value . '%');
            } else {
                $query->where($column_name, $value);
            }
        }
    }

    public function applyCustomFilters(&$fields, &$query) {

    }

    public function filterByCustomFields($data, $fields) {
        foreach ($fields as $column_name => $column_search) {
            $data = $data->where($column_name, $column_search['value']);
        }

        return $data;
    }

    public function sortByCustomField($data, $sort) {
        $column_name = $sort[0];
        $order = $sort[1];

        if ($order === 'ASC') {
            return $data->sortBy($column_name);
        } else {
            return $data->sortByDesc($column_name);
        }
    }

    public function getFiltered(Request $request) {
        $fields = $request->post();

        $page = $fields['page'] ?? 1;
        $per_page = $fields['per_page']['value'] ?? 25;
        $sort = $fields['sort'] ?? null;
        $hash = $fields['hash'] ?? '';

        $custom_fields_filters = [];
        $custom_fields_sort = [];

        unset($fields['page']);
        unset($fields['per_page']);
        unset($fields['sort']);
        unset($fields['hash']);

        $query = $this->model->query()->select($this->model->getTable() . '.*');
        $this->query = $query;

        if (!empty($this->auto_load_relations)) {
            $query->with($this->auto_load_relations);
        }

        $this->applyCustomFilters($fields, $query);

        foreach ($fields as $column_name => $column_search) {
            if (!empty($this->model->custom_fields) && in_array($column_name, $this->model->custom_fields)) {
                $column_name = str_replace('->', '.', $column_name);
                $custom_fields_filters[$column_name] = $column_search;
                continue;
            }

            if (strpos($column_name, '->') !== false) {
                $parts = explode('->', $column_name);
                $column_name = array_pop($parts);
                $relation = implode('.', $parts);

                $query->whereHas($relation, function($query) use ($column_name, $column_search) {
                    $this->search($query, $column_name, $column_search);
                });
            } else {
                $this->search($query, $column_name, $column_search);
            }
        }

        if (!empty($sort)) {
            [$by_field, $order] = explode(',', $sort);

            $by_field_data = $this->getDataByName($by_field);
            $parts = explode('->', $by_field_data);

            if (count($parts) > 1) {
                $columnName = array_pop($parts);
                $relation = implode('.', $parts);
                $by_field = $this->joinEagerLoadedColumn($query, $relation, $columnName);
            }

            if (!empty($this->model->custom_fields) && in_array($by_field, $this->model->custom_fields)) {
                $custom_fields_sort = [$by_field, $order];
            } else {
                $query->orderBy($by_field, $order);
            }
        }

        if (!empty($custom_fields_filters) || !empty($custom_fields_sort)) {
            $data = $query->get();

            if (!empty($custom_fields_filters)) {
                $data = $this->filterByCustomFields($data, $custom_fields_filters);
            }

            if (!empty($custom_fields_sort)) {
                $data = $this->sortByCustomField($data, $custom_fields_sort);
            }

            $query = $data;
        }

        $this->filtered_query_without_pagination = clone $query;

        $total = $query->count();

        $skip = $per_page*($page-1);

        $query = $query->skip($skip)->take($per_page);

        if ($query instanceof EloquentBuilder) {
            $items = $query->get();
        } else {
            $items = $query;
        }

        $table_content = view('crud.tbody',
                             [
                                 'fields' => $this->tableFields,
                                 'items' => $items,
                                 'route' => $this->route,
                                 'row_buttons' => $this->row_buttons,
                             ])->render();

        return [
                'table' => $table_content,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'per_page' => $per_page,
                'page' => $page,
                'hash' => $hash,
               ];
    }

    private function getDataByName($name) {
        return $this->tableFields[$name]['data'] ?? $name;
    }

    private function joinEagerLoadedColumn($query, $relation, $relationColumn)
    {
        $table     = '';
        $lastQuery = $query;

        foreach (explode('.', $relation) as $eachRelation) {
            $model = $lastQuery->getRelation($eachRelation);

            switch (true) {
                case $model instanceof BelongsToMany:
                    $pivot   = $model->getTable();
                    $pivotPK = $model->getExistenceCompareKey();
                    $pivotFK = $model->getQualifiedParentKeyName();
                    $this->performJoin($pivot, $pivotPK, $pivotFK);

                    $related = $model->getRelated();
                    $table   = $related->getTable();
                    $tablePK = $related->getForeignKey();
                    $foreign = $pivot . '.' . $tablePK;
                    $other   = $related->getQualifiedKeyName();

                    $lastQuery->addSelect($table . '.' . $relationColumn);
                    $this->performJoin($table, $foreign, $other);

                    break;

                case $model instanceof HasOneOrMany:
                    $table     = $model->getRelated()->getTable();
                    $foreign   = $model->getQualifiedForeignKeyName();
                    $other     = $model->getQualifiedParentKeyName();
                    break;

                case $model instanceof BelongsTo:
                    $table     = $model->getRelated()->getTable();
                    $foreign   = $model->getQualifiedForeignKeyName();
                    $other     = $model->getQualifiedOwnerKeyName();
                    break;

                default:
                    throw new \Exception('Relation ' . get_class($model) . ' is not yet supported.');
            }

            $this->performJoin($table, $foreign, $other);
            $lastQuery = $model->getQuery();
        }

        return $table . '.' . $relationColumn;
    }

    private function performJoin($table, $foreign, $other, $type = 'left')
    {
        $joins = [];
        foreach ((array) $this->getBaseQueryBuilder()->joins as $key => $join) {
            $joins[] = $join->table;
        }

        if (! in_array($table, $joins)) {
            $this->getBaseQueryBuilder()->join($table, $foreign, '=', $other, $type);
        }
    }

    protected function getBaseQueryBuilder($instance = null)
    {
        if (! $instance) {
            $instance = $this->query;
        }

        if ($instance instanceof EloquentBuilder) {
            return $instance->getQuery();
        }

        return $instance;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( string $custom_view = null, array $additional_data = [] )
    {
        return view($custom_view ?? 'crud.create_edit', array_merge([
            'fields' => $this->createFields,
            'title' => $this->title.'. Создание',
            'route' => $this->route,
            'form_buttons' => Auth::guard('driver')->check() ? $this->form_buttons_driver : $this->form_buttons,
            'item' => \session()->get('item'),
            'type' => 'create',
            'append_js' => $this->append_js,
        ], $additional_data));
    }

    public function manyToManyAttach(&$model, &$request) {
        if (isset($model->manyToManyRelations)) {
            foreach ($model->manyToManyRelations as $name) {
                if ($request->has($name)) {

                    $value = $request->get($name);

                    $model->{$name}()->detach();
                    if (!empty($value)) $model->{$name}()->attach($value);

                    $request->request->remove($name);
                }
            }
        }
    }

    public function getUserBelonging() {
        if (Auth::guard('organization')->check()) {
            return ['organization_id' => Auth::guard('organization')->id()];
        } elseif (Auth::guard('driver')->check()) {
            return [
                'organization_id' => Auth::guard('driver')->user()->organization_id,
                'driver_id' => Auth::guard('driver')->id()
            ];
        } else {
            return [];
        }
    }

    public function filterUserInput(Request &$request, string $type) {
        if ($request->isJson()) abort(404);

        if ($type === 'store') {
            $permitted_fields = array_keys($this->createFields);
        } elseif ($type === 'update') {
            $permitted_fields = array_keys($this->editFields);
        }

        $inputs = [$request->query, $request->request, $request->files, $request->attributes];

        foreach ($inputs as $input) {
            foreach ($input->keys() as $key) {
                if (!in_array($key, $permitted_fields, true)) {
                    $input->remove($key);
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $save_and_clone = $request->has('clone');

        $this->filterUserInput($request, 'store');

        $request->validate($this->createValidate);

        $this->request_data = array_merge($request->all(), $this->request_data, $this->getUserBelonging());

        $this->item = $this->model->create($this->request_data);
        $this->manyToManyAttach($this->item, $request);

        if ($request->ajax()) {
            return ['id' => $this->item->id, 'name' => $this->item->name ?? $this->item->number ?? '' ];
        } elseif ($save_and_clone) {
            return \redirect()->route($this->route . '.create')
                              ->with('item', $this->item)
                              ->with('success', 'Рейс сохранён. Сейчас перед вами форма нового рейса – можете редактировать!');
        } else {
            return redirect($this->route)->with('success', 'Сохранено!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->model->findOrFail($id);

        if (\request()->has('json')) return \response()->json($item);

        return view('crud.show', [
            'fields' => $this->showFields,
            'item' => $item,
            'title' => $this->title,
            'route' => $this->route,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $additional_data = [])
    {
        $item = $this->model->findOrFail($id);

        return view('crud.create_edit', array_merge([
            'fields' => $this->editFields,
            'item' => $item,
            'title' => $this->title.'. Редактирование',
            'route' => $this->route,
            'form_buttons' => $this->form_buttons,
            'type' => 'edit',
            'append_js' => $this->append_js,
        ], $additional_data));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $save_and_clone = $request->has('clone');

        $this->filterUserInput($request, 'update');

        $request->validate($this->editValidate);

        $this->item = $this->model->findOrFail($id);

        $this->request_data = array_merge($this->request_data, $request->all(), $this->getUserBelonging());

        $this->manyToManyAttach($this->item, $request);

        $this->item->update($this->request_data);

        if ($save_and_clone){
            return \redirect()->route($this->route . '.create')
                              ->with('item', $this->item)
                              ->with('success', 'Рейс сохранён. Сейчас перед вами форма нового рейса – можете редактировать!');
        } else {
            return redirect($this->route)->with('success', 'Сохранено!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);

        if (!empty($model->model_relations)) {
            $msg = '';

            foreach ($model->model_relations as $relation) {
                if (!empty($model->$relation) && $model->$relation->isNotEmpty()) {
                    $className = get_class($model->$relation()->getRelated());

                    $href = route($relation.'.index') . '#' . substr($this->route, 0, -1) . '_id=' . $id;

                    $msg .= '<br/><a href="'.$href.'" target="_blank"><b>' . $className::$model_name . '</b></a>' . '<br/>';

                    foreach ($model->$relation as $item) {
                        $msg .= '<a href="' . route($relation.'.show', $item->id) . '" target="_blank">' . ($item->name ?? $item->number ?? $item->date ?? $item->id) . '</a>' . '<br/>';
                    }
                }
            }

            if (!empty($msg)) {
                return response()->json([
                    'error'  => 'Ошибка удаления',
                    'msg'    => 'Объект связан с' . '<br/>' . $msg,
                    'status' => 'error',
                ]);
            }
        }

        if (isset($model->manyToManyRelations)) {
            foreach ($model->manyToManyRelations as $name) {
                $model->{$name}()->detach();
            }
        }

        if ($model->delete()) {
            return response()->json([
                'status' => 'success',
            ]);
        } else {
            abort(500);
        }
    }

    public function getRoute() {
        return $this->route;
    }

    public function getCreateFields() {
        return $this->createFields;
    }

    public function getTitle() {
        return $this->title;
    }

}
