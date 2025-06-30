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
                return $user->getRoleNames()->implode(', ') ?: '-';
            })
            ->addColumn('action', function (User $user) {
                return view('user.datatables-column._actions', compact('user'));
            })
            ->rawColumns(['action'])
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
        return $model->newQuery()->with('karyawan')
            ->whereHas('karyawan', function ($query) {
                $query->where('status_peg', 1);
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
                    'search' => '', // Menghapus label "Search" default
                    'searchPlaceholder' => 'Cari Karyawan...', // Placeholder untuk input pencarian
                ],
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
            Column::make('kd_karyawan')->title('ID Pegawai')->orderable(false),
            Column::make('name')->title('Nama'),
            Column::make('email')->title('Email'),
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
