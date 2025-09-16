<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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
            ->addColumn('kd_karyawan', function (User $user) {
                return $user->karyawan->kd_karyawan ?? '-';
            })
            ->addColumn('name', function (User $user) {
                $nama_lengkap = trim(($user->karyawan->gelar_depan ?? '') . ' ' . $user->karyawan->nama . '' . ($user->karyawan->gelar_belakang ?? ''));

                return $nama_lengkap;
            })
            ->addColumn('roles', function (User $user) {
                // Hanya tampilkan HRD roles
                $hrdRoles = collect($user->getRoleNames())
                    ->filter(function ($role) {
                        return str_starts_with($role, 'hrd_');
                    })
                    ->map(function ($role) {
                        // Tampilkan tanpa prefix untuk readability
                        $displayName = str_replace('hrd_', '', $role);
                        return '<span class="badge badge-success">' . $displayName . '</span>';
                    });
                
                return $hrdRoles->count() > 0 ? $hrdRoles->implode(' ') : '<span class="badge badge-secondary">Tidak ada role</span>';
            })
            ->addColumn('action', function (User $user) {
                return view('user.datatables-column._actions', compact('user'));
            })
            ->rawColumns(['roles', 'action'])
            ->filterColumn('name', function ($query, $keyword) {
                $query->whereHas('karyawan', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()
            ->with(['karyawan', 'roles'])
            ->whereHas('karyawan', function ($query) {
                $query->where('status_peg', 1);
            })
            // Hanya tampilkan user yang memiliki HRD roles atau tidak memiliki role sama sekali
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'like', 'hrd_%');
                })->orWhereDoesntHave('roles');
            });
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->buttons([
                Button::make('reload'),
            ])
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['reload'],
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Cari User/Karyawan...',
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
            Column::make('id')->title('No.')->orderable(false)->searchable(false),
            Column::make('kd_karyawan')->title('ID Pegawai')->orderable(false)->searchable(true),
            Column::make('name')->title('Nama')->searchable(true),
            Column::make('email')->title('Email')->searchable(true),
            Column::make('roles')->title('Roles')->orderable(false)->searchable(false),
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
        return 'User_' . date('YmdHis');
    }
}
