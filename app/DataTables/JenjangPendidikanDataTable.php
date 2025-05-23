<?php

namespace App\DataTables;

use App\Models\JenjangPendidikan;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class JenjangPendidikanDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('name', function (JenjangPendidikan $row) {
                return $row->jenjang_didik;
            })
            ->addColumn('nilaiIndex', function (JenjangPendidikan $row) {
                return $row->nilaiIndex;
            })
            ->addColumn('action', function (JenjangPendidikan $row) {
                return view('settings.jenjang_pendidikan.datatables-column._actions', compact('row'));
            })
            ->rawColumns(['action'])
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('jenjang_didik', 'like', "%{$keyword}%");
            });
    }

    public function query(JenjangPendidikan $model)
    {
        return $model->newQuery(); // Tidak ada orderBy untuk menghindari error SQL Server
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('jenjangpendidikan-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->order([[2, 'asc']]) // Urutkan berdasarkan kolom nilaiIndex (indeks 2)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    )
                    ->parameters([
                        'language' => [
                            'emptyTable' => 'Tidak ada data jenjang pendidikan.',
                            'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                            'infoEmpty' => 'Menampilkan 0 sampai 0 dari 0 entri',
                            'search' => 'Cari:',
                            'processing' => 'Memproses...',
                        ],
                    ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->title('No.')->orderable(false)->searchable(false),
            Column::make('name')->title('Jenjang Pendidikan'),
            Column::make('nilaiIndex')->title('Nilai Indeks')->orderable(true),
            Column::make('action')->title('Action')->orderable(false)->searchable(false),
        ];
    }

    protected function filename()
    {
        return 'JenjangPendidikan_' . date('YmdHis');
    }
}