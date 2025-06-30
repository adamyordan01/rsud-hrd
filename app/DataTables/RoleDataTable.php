<?php

namespace App\DataTables;

use App\Models\Role;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
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
            ->addColumn('permissions', function (Role $role) {
                $permissions = $role->permissions;
                if ($permissions->count() > 3) {
                    $output = $permissions->take(3)->map(fn($permission) => '<span class="badge badge-light">' . $permission->name . '</span>')->implode(' ');
                    $output .= '<span class="badge badge-light">dan ' . ($permissions->count() - 3) . ' permission lainnya</span>';
                    return $output;
                } elseif ($permissions->count() > 0) {
                    return $permissions->map(fn($permission) => '<span class="badge badge-light">' . $permission->name . '</span>')->implode(' ');
                }
                return '<span class="badge badge-light">-</span>';
            })
            ->addColumn('action', function (Role $role) {
                return view('roles.datatables-column._actions', compact('role'));
            })
            ->rawColumns(['permissions', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->newQuery()
            ->withCount(['permissions', 'users'])
            ->with('permissions');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('role-table')
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
            Column::make('name')->title('Nama Role'),
            Column::make('permissions')->title('Permission'),
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
        return 'Role_' . date('YmdHis');
    }
}
