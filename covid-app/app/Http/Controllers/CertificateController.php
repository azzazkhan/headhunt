<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\DataTables\CertificateDataTable;

class CertificateController extends Controller
{
    public function index(CertificateDataTable $dataTable) {
        return $dataTable->render('certificates.index');
    }
    public function show() {}
    public function edit() {}
    public function update() {}
    public function destroy() {}
}
