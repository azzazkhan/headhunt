@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-headaer">
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
                            <i class="fas fa tachometer-alt"></i>
                            {{trans('lang.dashboard')}}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('certificates.index') }}">Certificates</a>
                    </li>
                    <li class="breadcrumb-item active">Certificates List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="cleaarfix">
                @include('flash::message')
                <div class="card shadow-sm">
                    <div class="card-header">
                        <nav class="nav nav-tabs d-flex flex-md-row flex-column-reverse align-items-start card-header-tabs">
                            <ul class="d-flex flex-row">
                                <li class="nav-item">
                                    <a href="{!! url()->current() !!}" class="nav-link active">
                                        <i class="fa fa-list mr-2"></i> Certificates List
                                    </a>
                                </li>
                            </ul>
                            @include('layouts.right_toolbar', compact('dataTable'))
                        </nav>
                    </div>
                    <div class="card-body">
                        @include('certificates.table')
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection