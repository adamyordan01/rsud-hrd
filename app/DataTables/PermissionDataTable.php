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
                if ($roles->count() > 0) {
                    return $roles->map(function($role) {
                        // Tampilkan nama role tanpa prefix hrd_ untuk readability
                        $displayName = str_replace('hrd_', '', $role->name);
                        return '<span class="badge badge-primary">' . $displayName . '</span>';
                    })->implode(' ');
                }
                return '<span class="badge badge-secondary">Tidak ada role</span>';
            })
            ->addColumn('action', function (Permission $permission) {
                return view('permissions.datatables-column._actions', compact('permission'));
            })
            // Filter untuk kolom name - support pencarian tanpa prefix hrd_
            ->filterColumn('name', function($query, $keyword) {
                // Bisa mencari dengan atau tanpa prefix hrd_
                $query->where(function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('name', 'like', "%hrd_{$keyword}%");
                });
            })
            // Filter untuk kolom description
            ->filterColumn('description', function($query, $keyword) {
                $query->where('description', 'like', "%{$keyword}%");
            })
            // Filter untuk kolom slug
            ->filterColumn('slug', function($query, $keyword) {
                $query->where('slug', 'like', "%{$keyword}%");
            })
            // Filter untuk kolom roles (mencari di nama role)
            ->filterColumn('roles', function($query, $keyword) {
                $query->whereHas('roles', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['roles', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Permission $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Permission $model)
    {
        return $model->newQuery()
            ->where('name', 'like', 'hrd_%') // Hanya tampilkan HRD permissions
            ->with('roles');
            // SQL Server Fix: No orderBy in query method to avoid subquery ORDER BY error
            // Ordering is handled in html() method instead
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
                    ->orderBy(1, 'asc') // Order by name column
                    ->buttons([
                        Button::make('reload'),
                    ])
                    ->parameters([
                        'dom' => 'Bfrtip',
                        'buttons' => ['reload'],
                        'language' => [
                            'search' => '',
                            'searchPlaceholder' => 'Cari Permission...',
                            'lengthMenu' => 'Tampilkan _MENU_ data per halaman',
                            'zeroRecords' => 'Tidak ada data yang ditemukan',
                            'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            'infoEmpty' => 'Menampilkan 0 sampai 0 dari 0 data',
                            'infoFiltered' => '(difilter dari _MAX_ total data)',
                            'paginate' => [
                                'first' => 'Pertama',
                                'last' => 'Terakhir',
                                'next' => 'Selanjutnya',
                                'previous' => 'Sebelumnya'
                            ]
                        ],
                        'pageLength' => 10,
                        'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
                        'searching' => true,
                        'processing' => true,
                        'serverSide' => true,
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
            Column::make('id')->title('No.')->orderable(false)->searchable(false)->width(50),
            Column::make('name')->title('Nama Permission')->searchable(true)->render('function(data) { return data.replace("hrd_", ""); }'),
            Column::make('slug')->title('Slug')->searchable(true),
            Column::make('description')->title('Deskripsi')->searchable(true),
            Column::make('roles')->title('Roles')->orderable(false)->searchable(true),
            Column::make('action')->title('Action')->orderable(false)->searchable(false)->width(120),
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
