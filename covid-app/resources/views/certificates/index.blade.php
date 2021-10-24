@extends('layouts.app')

@section('content')
    @push('css_lib')
        @include('layouts.datatables_css')
    @endpush
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <!-- Page title -->
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold">
                        Certificates
                        <small class="mx-3">|</small>
                        <small>Certificates Management</small>
                    </h1>
                </div>
                <!-- Breadcrumb -->
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white float-sm-right rounded-pill px-4 py-2 d-none d-md-flex">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            {{trans('lang.dashboard')}}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('certificates') }}">Certificates</a>
                    </li>
                    <li class="breadcrumb-item active">Certificates List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="cleaarfix">
            @include('flash::message')
            <div class="card shadow-sm">
                <div class="card-header" style="display: none; visibility: hidden; opacity: 0; height: 0;">
                    <ul class="nav nav-tabs d-flex flex-md-row flex-column-reverse align-items-start card-header-tabs">
                        <div class="d-flex flex-row">
                            <li class="nav-item">
                                <a href="{!! url()->current() !!}" class="nav-link active">
                                    <i class="fa fa-list mr-2"></i> Certificates List
                                </a>
                            </li>
                        </div>
                        {{-- @include('layouts.right_toolbar', compact('dataTable')) --}}
                    </ul>
                </div>
                <div class="card-body">
                    <div id="dataTableBuilder_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <!-- Pagination and filter -->
                        <div class="row" style="display: none; visibility: hidden; opacity: 0;">
                            <!-- Pagination -->
                            <div class="col-lg-4 col-xs-12">
                                <div class="dataTables_length" id="dataTableBuilder_length">
                                    <label for="dataTable_itemsPerPage">
                                        Show
                                        <select name="dataTableBuilder_length" aria-controls="dataTableBuilder" id="dataTable_itemsPerPage" class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        entries
                                    </label>
                                </div>
                            </div>
                            <!-- Filter -->
                            <div class="ml-auto">
                                <div id="dataTableBuilder_filter" class="dataTables_filter">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="dataTableBuilder_search" id="dataTableBuilder_search" class="form-control" aria-label="Search" placeholder="Search" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-search" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Content wrapper -->
                        <div class="DTFC_ScrollWrapper" style="position: relative; clear: both; height: 731px; user-select: auto;">
                            <!-- Hidden content -->
                            <div class="DTFC_LeftWrapper" style="position: absolute; top: 0px; left: 0px; user-select: auto;" aria-hidden="true">
                                <div class="DTFC_LeftHeadWrapper" style="position: relative; top: 0px; left: 0px; overflow: hidden; user-select: auto;"></div>
                                <div class="DTFC_LeftBodyWrapper" style="position: relative; top: 0px; left: 0px; height: 0px; overflow: hidden; user-select: auto;">
                                    <div class="DTFC_LeftBodyLiner" style="position: relative; top: 0px; left: 0px; overflow-y: scroll; user-select: auto;"></div>
                                </div>
                                <div class="DTFC_LeftFootWrapper" style="position: relative; top: 0px; left: 0px; overflow: hidden; user-select: auto;"></div>
                            </div>
                            <!-- Content Table -->
                            <div class="dataTables_scroll" style="user-select: none;">
                                <div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%; user-select: auto;">
                                    <!-- Header Columns -->
                                    <div class="dataTables_scrollHeadInner" style="box-sizing: content-box; width: 1181.2px; padding-right: 0px; user-select: auto;">
                                        <table class="table dataTable no-footer" width="100%" role="grid" style="margin-left: 0px; width: 1181.2px; user-select: auto;">
                                            <!-- Header Row -->
                                            <thead style="user-select: auto;">
                                                <tr role="row" style="height: 51px; user-select: auto;">
                                                    <!-- Image column -->
                                                    <th title="Image" class="sorting_disabled" rowspan="1" colspan="1" data-column-index="0" style="width: 52.975px; user-select: auto;" aria-label="Image">Image</th>
                                                    <!-- Provider name column -->
                                                    <th title="Provider Name" class="sorting_disabled" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="1" style="width: 117.363px; user-select: auto;" aria-label="Provider Name: activate to sort column ascending">Provider Name</th>
                                                    <!-- Status column -->
                                                    <th title="Status" class="sorting_disabled" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="3" style="width: 55.4px; user-select: auto;" aria-label="Status: activate to sort column ascending">Status</th>
                                                    <!-- Submitted on column -->
                                                    <th title="Submitted On" class="sorting_disabled" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="3" style="width: 170.188px; user-select: auto;" aria-label="Submitted On: activate to sort column ascending">Submitted On</th>
                                                    <!-- Actions column -->
                                                    <th title="Action" width="80px" class="sorting_disabled" rowspan="1" colspan="1" data-column-index="8" style="width: 79.4px; user-select: auto;" aria-label="Action">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%; user-select: auto;">
                                    <table class="table dataTable no-footer" id="dataTableBuilder" width="100%" role="grid" aria-describedby="dataTableBuilder_info" style="width: 100%; user-select: auto;">
                                        <thead style="user-select: auto;">
                                            <tr role="row" style="height: 0px; user-select: auto;">
                                                <!-- Image column -->
                                                <th title="Image" class="sorting_disabled" rowspan="1" colspan="1" data-column-index="0" style="width: 52.975px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; user-select: auto;" aria-label="Image">
                                                    <div class="dataTables_sizing" style="height: 0px; overflow: hidden; user-select: auto;">Image</div>
                                                </th>
                                                <!-- Provider name column -->
                                                <th title="Provider Name" class="sorting_disabled" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="1" style="width: 117.363px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; user-select: auto;" aria-label="Provider Name: activate to sort column ascending">
                                                    <div class="dataTables_sizing" style="height: 0px; overflow: hidden; user-select: auto;">Provider Name</div>
                                                </th>
                                                <!-- Status column -->
                                                <th title="Status" class="sorting_disabled" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="2" style="width: 55.4px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; user-select: auto;" aria-label="Status: activate to sort column ascending">
                                                    <div class="dataTables_sizing" style="height: 0px; overflow: hidden; user-select: auto;">Status</div>
                                                </th>
                                                <!-- Submitted on column -->
                                                <th title="Submitted On" class="sorting_disabled" aria-controls="dataTableBuilder" rowspan="1" colspan="1" data-column-index="3" style="width: 170.188px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; user-select: auto;" aria-label="Submitted On: activate to sort column ascending">
                                                    <div class="dataTables_sizing" style="height: 0px; overflow: hidden; user-select: auto;">Submitted On</div>
                                                </th>
                                                <!-- Actioins column -->
                                                <th title="Action" width="80px" class="sorting_disabled" rowspan="1" colspan="1" data-column-index="8" style="width: 79.4px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; user-select: auto;" aria-label="Action">
                                                    <div class="dataTables_sizing" style="height: 0px; overflow: hidden; user-select: auto;">Action</div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody style="user-select: auto;">
                                            @foreach ($certificates as $certificate)
                                                <tr class="{{ $loop->even ? 'even' : 'odd' }}" style="height: 74px; user-select: auto;">
                                                    <!-- Image -->
                                                    <td class="sorting_1" style="user-select: auto;">
                                                        <img
                                                            class="rounded"
                                                            src="{!!
                                                                asset(sprintf(
                                                                    'storage/certificates/thumbnails/%s.jpg',
                                                                    $certificate->ref
                                                                ))
                                                            !!}"
                                                            style="height: 50px; user-select: auto;"
                                                            alt="image_default"
                                                        />
                                                    </td>
                                                    <!-- Provider Name -->
                                                    <td style="user-select: auto;">
                                                        <a href="{{ route('users.edit', $certificate->user) }}">
                                                            {{ $certificate->user->name }}
                                                        </a>
                                                    </td>
                                                    <!-- Status -->
                                                    <td style="user-select: auto;">
                                                        @php
                                                            $badgeColor = "secondary";
                                                            if ($certificate->status === "approved")
                                                                $badgeColor = "success";
                                                            else if ($certificate->status === "rejected")
                                                                $badgeColor = "danger";
                                                        @endphp
                                                        <span class="badge badge-{{ $badgeColor }} p-2" style="user-select: auto;">
                                                            {{ ucfirst($certificate->status) }}
                                                        </span>
                                                    </td>
                                                    <!-- Submitted On -->
                                                    <td style="user-select: auto;">
                                                        <span
                                                            data-toggle="tooltip"
                                                            data-placement="left"
                                                            title="{{ $certificate->created_at->format('D jS M y (h:i:s A)') }}"
                                                            style="user-select: auto;"
                                                        >
                                                            {{ $certificate->created_at->diffForHumans() }}
                                                        </span>
                                                    </td>
                                                    <!-- Actions -->
                                                    <td style="user-select: auto;"><div class="btn-group btn-group-sm" style="user-select: auto;">
                                                        @if ($certificate->status === 'pending')
                                                            <!-- Approve -->
                                                            <form method="POST" action="{!! url('certificates/' . $certificate->ref) !!}" accept-charset="UTF-8" style="user-select: auto;">
                                                                @method('PUT')
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved" />
                                                                <button type="submit" class="btn btn-link text-success" onclick="return confirm('Are you sure? This is one time action!')" style="user-select: auto;">
                                                                    <i class="fas fa-check" style="user-select: auto;"></i>
                                                                </button>
                                                            </form>
                                                            <!-- Reject -->
                                                            <form method="POST" action="{!! url('certificates/' . $certificate->ref) !!}" accept-charset="UTF-8" style="user-select: auto;">
                                                                @method('PUT')
                                                                @csrf
                                                                <input type="hidden" name="status" value="rejected" />
                                                                <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Are you sure? This is one time action!')" style="user-select: auto;">
                                                                    <i class="fas fa-ban" style="user-select: auto;"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <!-- Delete -->
                                                        {{-- <form method="POST" action="{!! url('certificates/' . $certificate->ref) !!}" accept-charset="UTF-8" style="user-select: auto;">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Are you sure?')" style="user-select: auto;">
                                                                <i class="fas fa-trash" style="user-select: auto;"></i>
                                                            </button>
                                                        </form> --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Pagination information & items count -->
                        <div class="dataTables_info" id="dataTableBuilder_info" role="status" aria-live="polite" style="user-select: auto;">
                            Showing 1 to {{ $certificates->count() }} of {{ $certificates->count() }} entries
                        </div>
                    </div>
                    {{-- @include('certificates.table') --}}
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- @push('scripts_lib')
        @include('layouts.datatables_js')
        {!! $dataTable->scripts() !!}
    @endpush --}}
@endsection