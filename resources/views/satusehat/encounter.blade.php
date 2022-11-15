@extends('adminlte::page')
@section('title', 'Encounter - Satu Sehat')
@section('content_header')
    <h1>Encounter</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Data Encounter" theme="secondary" collapsible>
                {{-- <form action="{{ route('satusehat.organization.index') }}" method="get">
                    <x-adminlte-input name="partOf" label="Part Of Organization" placeholder="Masukan ID Part Of Organization"
                        value="{{ $request->partOf }}">
                        <x-slot name="appendSlot">
                            <x-adminlte-button type="submit" class="withLoad" theme="primary" label="Cari Organization" />
                        </x-slot>
                        <x-slot name="prependSlot">
                            <div class="input-group-text text-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </form> --}}
                <x-adminlte-button label="Create Encounter" theme="success" title="Create Encounter" icon="fas fa-plus"
                    onclick="window.location='{{ route('satusehat.encounter.create') }}'" />
                <br><br>
                @php
                    $heads = ['No.', 'Identifier', 'Start', 'End', 'Location','Patient', 'Practitioner',  'Status', 'Action'];
                @endphp
                <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" hoverable bordered compressed>
                    @foreach ($encounters as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->identifier_id }}</td>
                            <td>{{ Carbon\Carbon::parse($item->period_start) }}</td>
                            <td>{{ $item->period_end }}</td>
                            <td>{{ $item->location_name }}</td>
                            <td>{{ $item->patient_name }} / {{ $item->patient_id }}</td>
                            <td>{{ $item->practitioner_name }} / {{ $item->practitioner_id }}</td>
                            <td>{{ $item->status }}</td>
                            <td>

                            </td>

                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
