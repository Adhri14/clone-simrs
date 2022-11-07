@extends('adminlte::page')
@section('title', 'Patient')
@section('content_header')
    <h1>Patient</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Data Antrian" theme="secondary" collapsible>
                <form action="{{ route('satusehat.patient.index') }}" method="get">
                    <x-adminlte-input name="nik" label="NIK Pasien" placeholder="Masukan NIK Pasien"
                        value="{{ $request->nik }}">
                        <x-slot name="appendSlot">
                            <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Cari Pasien" />
                        </x-slot>
                        <x-slot name="prependSlot">
                            <div class="input-group-text text-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </form>
                <form action="{{ route('satusehat.patient.index') }}" method="get">
                    <x-adminlte-input name="id" label="ID IHS Pasien" placeholder="Masukan ID IHS Pasien"
                        value="{{ $request->id }}">
                        <x-slot name="appendSlot">
                            <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Cari Pasien" />
                        </x-slot>
                        <x-slot name="prependSlot">
                            <div class="input-group-text text-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </form>
            </x-adminlte-card>
        </div>
        @isset($patient)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $patient->name[0]->text }}" desc="{{ $patient->id }}" theme="primary"
                    img="https://picsum.photos/id/1/100">
                    {{-- <x-adminlte-profile-col-item title="Followers" text="125" url="#" />
                    <x-adminlte-profile-col-item title="Following" text="243" url="#" />
                    <x-adminlte-profile-col-item title="Posts" text="37" url="#" /> --}}
                    <ul class="nav flex-column col-md-12">
                        @isset($patient->identifier)
                            @foreach ($patient->identifier as $item)
                                <li class="nav-item">
                                    <b class="nav-link">
                                        <a href="{{ $item->system }}"> Identifier </a>
                                        <b class="float-right ">{{ $item->value }}</b>
                                    </b>
                                </li>
                            @endforeach
                        @endisset
                        @isset($patient->telecom)
                            @foreach ($patient->telecom as $item)
                                <li class="nav-item">
                                    <b class="nav-link">
                                        {{ Str::ucfirst($item->system) }}
                                        <b class="float-right ">{{ $item->value }}</b>
                                    </b>
                                </li>
                            @endforeach
                        @endisset
                        <li class="nav-item">
                            <b class="nav-link">Gender <b class="float-right ">{{ $patient->gender }}</b></b>
                        </li>
                        <li class="nav-item">
                            <b class="nav-link">Last Update <b
                                    class="float-right ">{{ \Carbon\Carbon::parse($patient->meta->lastUpdated) }}</b></b>
                        </li>
                    </ul>
                </x-adminlte-profile-widget>
            </div>
        @endisset

    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
