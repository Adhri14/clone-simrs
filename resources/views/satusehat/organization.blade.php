@extends('adminlte::page')
@section('title', 'Organization - Satu Sehat')
@section('content_header')
    <h1>Organization</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Filter Pencarian Organization Satu Sehat" theme="secondary" collapsible>
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
                <x-adminlte-button label="Create Organization" theme="success" title="Create Organization"
                    icon="fas fa-plus" id="btnCreateOrganization" />
            </x-adminlte-card>
        </div>
        @if (isset($organization))
            <div class="col-12">
                <x-adminlte-card title="Pencarian Data Organization Satu Sehat" theme="secondary" collapsible>
                    @php
                        $heads = ['Part Of Organization', 'Identifier', 'Nama', 'Phone', 'Email', 'Kota / Kab', 'Status', 'Last Update', 'Action'];
                        $config['scrollY'] = '300px';
                        $config['scrollCollapse'] = true;
                        $config['paging'] = false;
                        $config['info'] = false;
                    @endphp
                    <x-adminlte-datatable id="table1" class="text-xs" :heads="$heads" :config="$config" hoverable bordered compressed>
                        @foreach ($organization as $item)
                            <tr>
                                <td>
                                    {{ $item->resource->partOf->reference }}
                                </td>
                                <td>
                                    @isset($item->resource->identifier)
                                        @foreach ($item->resource->identifier as $identifier)
                                            {{ $identifier->value }}
                                        @endforeach
                                    @endisset
                                </td>
                                <td>{{ $item->resource->name }}</td>
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
                                    @isset($item->resource->telecom)
                                        @foreach ($item->resource->telecom as $telecom)
                                            @if ($telecom->system == 'email')
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
                                    <x-adminlte-button class="btn-xs btnEdit" theme="warning" icon="fas fa-edit"
                                        title="Edit User {{ $item->resource->name }}"
                                        data-id="{{ $item->resource->id }}" />
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        @endif
        <div class="col-12">
            <x-adminlte-card title="Data Organization SIMRS" theme="secondary" collapsible>
                @php
                    $heads = ['Part Of Organization', 'Identifier', 'Nama', 'Phone', 'Email', 'Kota / Kab', 'Status', 'Last Update'];
                    $config['scrollY'] = '300px';
                    $config['scrollCollapse'] = true;
                    $config['paging'] = false;
                    $config['info'] = false;
                @endphp
                <x-adminlte-datatable id="table2" class="text-xs" :heads="$heads" :config="$config" hoverable bordered compressed>
                    @foreach ($organization_simrs as $organization)
                        <tr>
                            <td>{{ $organization->part_of_id }}</td>
                            <td>{{ $organization->identifier_id }}</td>
                            <td>{{ $organization->name }}</td>
                            <td>{{ $organization->phone }}</td>
                            <td>{{ $organization->email }}</td>
                            <td>{{ $organization->city }}</td>
                            <td>
                                @if ($organization->active)
                                    Aktif
                                @else
                                    Tidak Aktif
                                @endif
                            </td>
                            <td>{{ $organization->updated_at }}</td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalOrganization" title="Organization" size="lg" theme="success" v-centered>
        <form name="formOrganization" id="formOrganization">
            <div class="row">
                <div class="col-6">
                    <x-adminlte-input name="organization_id" label="Part Of Organization"
                        value="Organization/{{ env('SATUSEHAT_ORGANIZATION_ID') }}" enable-old-support />
                    <x-adminlte-input name="id" label="ID" enable-old-support readonly />
                    <x-adminlte-input name="identifier" label="Identifier" enable-old-support />
                    <x-adminlte-input name="name" label="Nama" enable-old-support />
                    <x-adminlte-input name="phone" label="No Telepon" enable-old-support />
                    <x-adminlte-input name="email" type="email" label="Email" enable-old-support />
                    <x-adminlte-input name="url" label="Url Website" enable-old-support />
                </div>
                <div class="col-6">
                    <x-adminlte-select2 name="province" label="Provinsi">
                        <option value="" selected disabled>PILIH POLIKLINIK</option>
                        @foreach ($provinsi as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <input type="hidden" name="cityText" id="cityText">
                    <x-adminlte-select2 name="city" label="Kota / Kabupaten">
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="district" label="Kecamatan">
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="village" label="Desa">
                    </x-adminlte-select2>
                    <x-adminlte-input name="address" label="Alamat Jalan" enable-old-support />
                    <x-adminlte-input name="postalCode" label="Postal Code" enable-old-support />
                </div>
            </div>
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button id="btnStore" class="mr-auto" icon="fas fa-save" theme="success" label="Simpan" />
            <x-adminlte-button id="btnUpdate" class="mr-auto" icon="fas fa-edit" theme="warning" label="Update" />
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#btnCreateOrganization').click(function() {
                $.LoadingOverlay("show");
                $('#formOrganization').trigger("reset");
                $('#btnStore').show();
                $('#btnUpdate').hide();
                $('#modalOrganization').modal('show');
                $.LoadingOverlay("hide");
            });
            $('body').on('click', '.btnEdit', function() {
                $.LoadingOverlay("show");
                var id = $(this).data('id');
                var url = "{{ route('api.satusehat.organization_index') }}" + "/" + id;
                $.get(url, function(response) {
                    console.log(response);
                    $('#id').val(response.id);
                    $('#identifier').val(response.identifier[0].value);
                    $('#name').val(response.name);
                    $('#phone').val(response.telecom[0].value);
                    $('#email').val(response.telecom[1].value);
                    $('#url').val(response.telecom[2].value);
                    $('#address').val(response.address[0].line[0]);
                    $('#postalCode').val(response.address[0].postalCode);
                    $('#organization_id').val(response.partOf.reference);
                    $("#province").val(response.address[0].extension[0].extension[0].valueCode)
                        .change();
                    $("#city").append($(new Option(response.address[0].extension[0]
                        .extension[1].valueCode, response.address[0].extension[0]
                        .extension[1].valueCode)));
                    $("#district").append($(new Option(response.address[0].extension[0]
                        .extension[2].valueCode, response.address[0].extension[0]
                        .extension[2].valueCode)));
                    $("#village").append($(new Option(response.address[0].extension[0]
                        .extension[3].valueCode, response.address[0].extension[0]
                        .extension[3].valueCode)));

                    $('#btnStore').hide();
                    $('#btnUpdate').show();
                    $('#modalOrganization').modal('show');
                    $.LoadingOverlay("hide");
                })
            });
            $('#btnStore').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                $.ajax({
                    data: $('#formOrganization').serialize(),
                    url: "{{ route('api.satusehat.organization_store_api') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        swal.fire(
                            'Success',
                            'Data Berhasil Disimpan',
                            'success'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show");
                                location.reload();
                            }
                        });
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.original.data,
                            'error'
                        );
                    }
                });
                $.LoadingOverlay("hide");
            });
            $('#btnUpdate').click(function(e) {
                $.LoadingOverlay("show");
                e.preventDefault();
                var id = $('#id').val();
                var url = "{{ route('api.satusehat.organization_index') }}" + "/update/" + id;
                $.ajax({
                    data: $('#formOrganization').serialize(),
                    url: url,
                    type: "PUT",
                    dataType: 'json',
                    success: function(data) {
                        swal.fire(
                            'Success',
                            'Data Berhasil Disimpan',
                            'success'
                        ).then(okay => {
                            if (okay) {
                                $.LoadingOverlay("show");
                                location.reload();
                            }
                        });
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.original.data,
                            'error'
                        );
                    }
                });
                $.LoadingOverlay("hide");
            });
            $("#city").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('get_city') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        code = $("#province").find(":selected").val();
                        if (code.length != 0) {
                            return {
                                code: code,
                                search: params.term // search term
                            };
                        } else {
                            return alert('Silahkan Pilih Provinsi Terlebih Dahulu !')
                        }
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $("#district").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('get_district') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        code = $("#city").find(":selected").val();
                        if (code != null) {
                            return {
                                code: code,
                                search: params.term // search term
                            };
                        } else {
                            return alert('Silahkan Pilih Kota / Kabupaten Terlebih Dahulu !')
                        }
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $("#village").select2({
                theme: "bootstrap4",
                ajax: {
                    url: "{{ route('get_village') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        code = $("#district").find(":selected").val();
                        if (code != null) {
                            return {
                                code: code,
                                search: params.term // search term
                            };
                        } else {
                            return alert('Silahkan Pilih Kecamatan Terlebih Dahulu !')
                        }
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection
