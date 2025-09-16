<?php

namespace App\DataTables;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
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
            ->addColumn('permissions_count', function (Role $role) {
                return $role->permissions->count();
            })
            ->addColumn('users_count', function (Role $role) {
                return $role->users()->count();
            })
            ->addColumn('permissions', function (Role $role) {
                $permissions = $role->permissions;
                if ($permissions->count() > 3) {
                    $output = $permissions->take(3)->map(function($permission) {
                        // Tampilkan nama permission tanpa prefix hrd_ untuk readability
                        $displayName = str_replace('hrd_', '', $permission->name);
                        return '<span class="badge badge-light">' . $displayName . '</span>';
                    })->implode(' ');
                    $output .= '<span class="badge badge-info">+' . ($permissions->count() - 3) . ' lainnya</span>';
                    return $output;
                } elseif ($permissions->count() > 0) {
                    return $permissions->map(function($permission) {
                        $displayName = str_replace('hrd_', '', $permission->name);
                        return '<span class="badge badge-light">' . $displayName . '</span>';
                    })->implode(' ');
                }
                return '<span class="badge badge-secondary">Tidak ada permission</span>';
            })
            ->addColumn('action', function (Role $role) {
                return view('roles.datatables-column._actions', compact('role'));
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
            // Filter untuk kolom level
            ->filterColumn('level', function($query, $keyword) {
                if (is_numeric($keyword)) {
                    $query->where('level', $keyword);
                }
            })
            // Filter untuk kolom permissions (mencari di nama permission)
            ->filterColumn('permissions', function($query, $keyword) {
                $query->whereHas('permissions', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                });
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
        // Pendekatan sederhana untuk menghindari SQL Server ORDER BY issues
        return $model->newQuery()
            ->where('name', 'like', 'hrd_%')
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
                    ->orderBy(1, 'asc') // Order by name column
                    ->buttons([
                        Button::make('reload'),
                    ])
                    ->parameters([
                        'dom' => 'Bfrtip',
                        'buttons' => ['reload'],
                        'language' => [
                            'search' => '',
                            'searchPlaceholder' => 'Cari Role...',
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
            Column::make('name')->title('Nama Role')->searchable(true)->render('function(data) { return data.replace("hrd_", ""); }'),
            Column::make('description')->title('Deskripsi')->searchable(true),
            Column::make('level')->title('Level')->width(80)->searchable(true),
            Column::make('users_count')->title('Users')->width(80)->orderable(true)->searchable(false),
            Column::make('permissions_count')->title('Permissions')->width(100)->orderable(true)->searchable(false),
            Column::make('permissions')->title('Preview Permissions')->orderable(false)->searchable(true),
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
        return 'Role_' . date('YmdHis');
    }
}
