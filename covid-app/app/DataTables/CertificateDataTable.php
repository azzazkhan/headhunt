<?php

namespace App\DataTables;

use App\Certificate;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class CertificateDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('image', function ($category) {
                return '<img class="rounded" style="height: 50px;" src="' . url(Storage::url('certificates/' . $certificate->ref . '.jpg')) . '" alt="Certificate for ' . $category->user->name .'">';
            })
            ->editColumn('status', function ($category) {
                return $category->status;
            })
            ->editColumn('user', function ($category) {
                return $category->user->name;
            })
            ->editColumn('created_at', function ($category) {
                return $category->created_at;
            })
            ->addColumn('action', 'certificates.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));


        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Certificate $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Certificate $model)
    {
        return $model->newQuery()->select('certificates.*');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
                    ->setTableId('certificates-table')
                    ->dom('Bfrtip')
                    ->orderBy(1);
                    // ->buttons(
                    //     Button::make('create'),
                    //     Button::make('export'),
                    //     Button::make('print'),
                    //     Button::make('reset'),
                    //     Button::make('reload')
                    // );
    }

    /**
     * Export PDF using DomPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'image',
                'title' => 'Thumbnail',
                'searchable' => false,
                'orderable' => false,
                'exportable' => false,
            ],
            [
                'data' => 'status',
                'title' => 'Status',
            ],
            [
                'data' => 'user',
                'title' => 'Provider Name',
            ],
            [
                'data' => 'created_at',
                'title' => 'Submitted At',
            ]
        ];

        return $columns;

        // return [
        //     Column::computed('action')
        //           ->exportable(false)
        //           ->printable(false)
        //           ->width(60)
        //           ->addClass('text-center'),
        //     Column::make('id'),
        //     Column::make('image')
        //           ->exportable(false)
        //           ->width(60)
        //           ->addClass('text-center')
        //           ->searchable(false)
        //           ->orderable(false),
        //     Column::make('ref'),
        //     Column::make('status'),
        //     Column::make('created_at'),
        // ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Certificates_' . date('YmdHis');
    }
}