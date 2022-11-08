@extends('adminlte::page')
@section('title', 'Organization - Satu Sehat')
@section('content_header')
    <h1>Organization</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Organization" theme="secondary" collapsible>
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
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
