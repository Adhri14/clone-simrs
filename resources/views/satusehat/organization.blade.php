@extends('adminlte::page')
@section('title', 'Organization - Satu Sehat')
@section('content_header')
    <h1>Organization</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Organization" theme="secondary" collapsible>
                <form action="{{ route('satusehat.organization.index') }}" method="get">
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
                </form>
                {{-- <form action="{{ route('satusehat.organization.index') }}" method="get">
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
                </form> --}}
                <x-adminlte-button label="Create Organization" theme="success" title="Create Organization"
                    icon="fas fa-plus" data-toggle="modal" data-target="#createOrganization" />
            </x-adminlte-card>
        </div>
        @if (isset($organization->total))
            <div class="col-12">
                <x-adminlte-card title="Data Organization" theme="secondary" collapsible>
                    @php
                        $heads = ['Nama', 'Identifier', 'Part Of', 'Phone', 'Kota / Kab', 'PostCode', 'Status', 'Last Update', 'Action'];
                        $config['scrollY'] = '300px';
                        $config['scrollCollapse'] = true;
                        $config['paging'] = false;
                        $config['info'] = false;
                    @endphp
                    <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" hoverable bordered compressed>
                        @foreach ($organization->entry as $item)
                            <tr>
                                <td>{{ $item->resource->name }}</td>
                                <td>
                                    @isset($item->resource->identifier)
                                        @foreach ($item->resource->identifier as $identifier)
                                            {{ $identifier->value }}
                                        @endforeach
                                    @endisset
                                </td>
                                <td>
                                    {{ $item->resource->partOf->reference }}
                                </td>
                                <td>
                                    @isset($item->resource->telecom)
                                        @foreach ($item->resource->telecom as $telecom)
                                            @if ($telecom->system == 'phone')
                                                {{ $telecom->value }}
                                            @endif
                                        @endforeach
                                    @endisset
                                </td>
                                <td>
                                    @isset($item->resource->address)
                                        @foreach ($item->resource->address as $address)
                                            {{ $address->city }}
                                        @endforeach
                                    @endisset
                                </td>
                                <td>
                                    @isset($item->resource->address)
                                        @foreach ($item->resource->address as $address)
                                            {{ $address->postalCode }}
                                        @endforeach
                                    @endisset
                                </td>
                                <td>
                                    @if ($item->resource->active)
                                        Aktif
                                    @else
                                        Tidak Aktif
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->resource->meta->lastUpdated) }}
                                </td>
                                <td>
                                    <x-adminlte-button class="btn-xs" theme="warning" icon="fas fa-edit"
                                        title="Edit User {{ $item->resource->name }}"
                                        onclick="window.location='{{ route('satusehat.organization.edit', $item->resource->id) }}'" />
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        @endif
        {{-- @isset($patient)
            <div class="col-md-4">
                <x-adminlte-profile-widget name="{{ $patient->name[0]->text }}" desc="{{ $patient->id }}" theme="primary"
                    img="https://picsum.photos/id/1/100">
                    <x-adminlte-profile-col-item title="Followers" text="125" url="#" />
                    <x-adminlte-profile-col-item title="Following" text="243" url="#" />
                    <x-adminlte-profile-col-item title="Posts" text="37" url="#" />
                    <ul class="nav flex-column col-md-12">
                        <li class="nav-item">
                            <b class="nav-link">
                                Nama
                                <b class="float-right ">{{ $patient->name[0]->text }}</b>
                            </b>
                        </li>
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
                        @isset($patient->address)
                            @foreach ($patient->address as $item)
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
                                    class="float-right ">{{ \Carbon\Carbon::parse($patient->meta->lastUpdated) }}</b></b>
                        </li>
                    </ul>
                </x-adminlte-profile-widget>
            </div>
        @endisset --}}
    </div>

    <x-adminlte-modal id="createOrganization" title="Create Organization" size="lg" theme="success" v-centered>
        <form action="{{ route('satusehat.organization.store') }}" id="formCreateOrganization" method="POST">
            @csrf
            <div class="row">
                <div class="col-6">
                    <x-adminlte-input name="organization_id" label="ID Organization"
                        value="{{ env('SATUSEHAT_ORGANIZATION_ID') }}" readonly enable-old-support required />
                    <x-adminlte-input name="organization_name" label="Part Of Organization"
                        value="{{ env('SATUSEHAT_ORGANIZATION_NAME') }}" readonly enable-old-support required />
                    <x-adminlte-input name="name" label="Nama" enable-old-support required />
                    <x-adminlte-input name="phone" label="No Telepon" enable-old-support required />
                    <x-adminlte-input name="email" type="email" label="Email" enable-old-support required />
                    <x-adminlte-input name="url" label="Url Website" enable-old-support required />
                </div>
                <div class="col-6">
                    <x-adminlte-input name="postalCode" label="Postal Code" enable-old-support required />
                    <x-adminlte-input name="postalCode" label="Postal Code" enable-old-support required />
                    <x-adminlte-input name="postalCode" label="Postal Code" enable-old-support required />

                </div>
            </div>
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button form="formCreateOrganization" class="mr-auto" type="submit" theme="success"
                label="Simpan" />
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
