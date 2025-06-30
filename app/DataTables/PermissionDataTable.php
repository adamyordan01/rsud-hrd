<?php

namespace App\DataTables;

use App\Models\Permission;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PermissionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('roles', function (Permission $permission) {
                $roles = $permission->roles;
                return $roles->count() > 0 ? $roles->pluck('name')->implode(', ') : '-';
            })
            ->addColumn('action', function (Permission $permission) {
                return view('permissions.datatables-column._actions', compact('permission'));
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Permission $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Permission $model)
    {
        return $model->newQuery()->with('roles');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('permission-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0)
                    ->buttons([
                        Button::make('reload'),
                    ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id')->title('No.')->orderable(false),
            Column::make('name')->title('Nama Permission'),
            Column::make('slug')->title('Slug'),
            Column::make('description')->title('Deskripsi'),
            Column::make('roles')->title('Roles'),
            Column::make('action')->title('Action')->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Permission_' . date('YmdHis');
    }
}
