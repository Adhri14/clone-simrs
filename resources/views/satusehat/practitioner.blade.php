@extends('adminlte::page')
@section('title', 'Practitioner - Satu Sehat')
@section('content_header')
    <h1>Practitioner</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Practitioner" theme="secondary" collapsible>
                <form action="{{ route('satusehat.practitioner.index') }}" method="get">
                    <x-adminlte-input name="nik" label="NIK Practitioner" placeholder="Masukan NIK Practitioner"
                        value="{{ $request->nik }}">
                        <x-slot name="appendSlot">
                            <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Cari Practitioner" />
                        </x-slot>
                        <x-slot name="prependSlot">
                            <div class="input-group-text text-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </form>
                <form action="{{ route('satusehat.practitioner.index') }}" method="get">
                    <x-adminlte-input name="id" label="ID Practitioner" placeholder="Masukan ID Practitioner"
                        value="{{ $request->id }}">
                        <x-slot name="appendSlot">
                            <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Cari Practitioner" />
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
        @isset($practitioner)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $practitioner->name[0]->text }}" desc="{{ $practitioner->id }}"
                    theme="primary" img="https://picsum.photos/id/1/100">
                    {{-- <x-adminlte-profile-col-item title="Followers" text="125" url="#" />
                    <x-adminlte-profile-col-item title="Following" text="243" url="#" />
                    <x-adminlte-profile-col-item title="Posts" text="37" url="#" /> --}}
                    <ul class="nav flex-column col-md-12">
                        <li class="nav-item">
                            <b class="nav-link">
                                Nama
                                <b class="float-right ">{{ $practitioner->name[0]->text }}</b>
                            </b>
                        </li>
                        @isset($practitioner->identifier)
                            @foreach ($practitioner->identifier as $item)
                                <li class="nav-item">
                                    <b class="nav-link">
                                        <a href="{{ $item->system }}"> Identifier </a>
                                        <b class="float-right ">{{ $item->value }}</b>
                                    </b>
                                </li>
                            @endforeach
                        @endisset
                        @isset($practitioner->telecom)
                            @foreach ($practitioner->telecom as $item)
                                <li class="nav-item">
                                    <b class="nav-link">
                                        {{ Str::ucfirst($item->system) }}
                                        <b class="float-right ">{{ $item->value }}</b>
                                    </b>
                                </li>
                            @endforeach
                        @endisset
                        <li class="nav-item">
                            <b class="nav-link">Gender <b class="float-right ">{{ $practitioner->gender }}</b></b>
                        </li>
                        @isset($practitioner->address)
                            @foreach ($practitioner->address as $item)
                                <li class="nav-item">
                                    <b class="nav-link">
                                        Kab / Kota
                                        <b class="float-right ">{{ $item->city }}</b>
                                    </b>
                                </li>
                            @endforeach
                        @endisset
                        <li class="nav-item">
                            <b class="nav-link">Last Update <b
                                    class="float-right ">{{ \Carbon\Carbon::parse($practitioner->meta->lastUpdated) }}</b></b>
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
