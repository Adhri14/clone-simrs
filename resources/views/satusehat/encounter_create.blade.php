@extends('adminlte::page')
@section('title', 'Encounter Create - Satu Sehat')
@section('content_header')
    <h1>Encounter Create</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-6">
            <x-adminlte-card title="Data Encounter" theme="secondary" collapsible>
                <form action="{{ route('satusehat.organization.index') }}" method="get">
                    <div class="row">
                        <div class="col-6">
                            <x-adminlte-input name="patient_id" igroup-size="sm" label="Identifier Patient"
                                placeholder="ID Patient" readonly>
                                <x-slot name="prependSlot">
                                    <x-adminlte-button id="btnPatient" theme="primary" label="Cari Patient" />
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="col-6">
                            <x-adminlte-input name="patient_name" igroup-size="sm" label="Nama Patient"
                                placeholder="Nama Patient" readonly>
                            </x-adminlte-input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-adminlte-input name="practitioner_id" igroup-size="sm" label="Identifier Practitioner"
                                placeholder="ID Practitioner" readonly>
                                <x-slot name="prependSlot">
                                    <x-adminlte-button id="btnPractitioner" theme="primary" label="Cari Practitioner" />
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="col-6">
                            <x-adminlte-input name="practitioner_name" igroup-size="sm" label="Nama Practitioner"
                                placeholder="Nama Practitioner" readonly>
                            </x-adminlte-input>
                        </div>
                    </div>
                </form>
                <x-adminlte-button label="Create Encounter" theme="success" title="Create Encounter" icon="fas fa-plus"
                    onclick="window.location='{{ route('satusehat.encounter.create') }}'" />
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="modalPatient" title="Pencarian Pasien" size="xl" theme="warning" v-centered>
        <div class="row">
            <div class="col-12">
                <x-adminlte-card title="Pencarian Patient" theme="secondary" collapsible>
                    <form name="formPatientByNIK" id="formPatientByNIK">
                        <x-adminlte-input name="nik" igroup-size="sm" label="NIK Pasien"
                            placeholder="Masukan NIK Pasien">
                            <x-slot name="appendSlot">
                                <x-adminlte-button id="btnPatientByNIK" theme="primary" label="Cari Pasien" />
                            </x-slot>
                            <x-slot name="prependSlot">
                                <div class="input-group-text text-primary">
                                    <i class="fas fa-search"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </form>
                    <form name="formPatientByName" id="formPatientByName">
                        <div class="row">
                            <div class="col-3">
                                @php
                                    $config = ['format' => 'YYYY-MM'];
                                @endphp
                                <x-adminlte-input-date name="birthdate" igroup-size="sm" label="Bulan Lahir"
                                    placeholder="Tahun Bulan Lahir" :config="$config" />
                            </div>
                            <div class="col-3">
                                <x-adminlte-select name="gender" igroup-size="sm" label="Gender">
                                    <option disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                    <option value="other">Lainnya</option>
                                    <option value="unknown">Tidak Diketahui</option>
                                </x-adminlte-select>
                            </div>
                            <div class="col-6">
                                <x-adminlte-input name="name" label="Nama Pasien" igroup-size="sm"
                                    placeholder="Nama Pasien">
                                    <x-slot name="appendSlot">
                                        <x-adminlte-button id="btnPatientByName" theme="primary" label="Cari Pasien" />
                                    </x-slot>
                                </x-adminlte-input>
                            </div>

                        </div>
                    </form>
                </x-adminlte-card>
            </div>
            <div class="col-12">
                <x-adminlte-card title="Hasil Pencarian Patient" theme="secondary" collapsible>
                    <table class="table table-bordered table-sm table-striped" id="table1">
                        <thead>
                            <th>Identifier</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </x-adminlte-card>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="modalPractitioner" title="Pencarian Practitioner" size="xl" theme="warning" v-centered>
        <div class="row">
            <div class="col-12">
                <x-adminlte-card title="Pencarian Practitioner" theme="secondary" collapsible>
                    <form name="formPractitionerByNIK" id="formPractitionerByNIK">
                        <x-adminlte-input name="nik_practitioner" igroup-size="sm" label="NIK Practitioner"
                            placeholder="Masukan NIK Practitioner">
                            <x-slot name="appendSlot">
                                <x-adminlte-button id="btnPractitionerByNIK" theme="primary" label="Cari Practitioner" />
                            </x-slot>
                            <x-slot name="prependSlot">
                                <div class="input-group-text text-primary">
                                    <i class="fas fa-search"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </form>
                    <form name="formPractitionerByName" id="formPractitionerByName">
                        <div class="row">
                            <div class="col-3">
                                @php
                                    $config = ['format' => 'YYYY-MM'];
                                @endphp
                                <x-adminlte-input-date name="birthdate_practitioner" igroup-size="sm" label="Bulan Lahir"
                                    placeholder="Tahun Bulan Lahir" :config="$config" />
                            </div>
                            <div class="col-3">
                                <x-adminlte-select name="gender_practitioner" igroup-size="sm" label="Gender">
                                    <option disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                    <option value="other">Lainnya</option>
                                    <option value="unknown">Tidak Diketahui</option>
                                </x-adminlte-select>
                            </div>
                            <div class="col-6">
                                <x-adminlte-input name="name_practitioner" label="Nama Practitioner" igroup-size="sm"
                                    placeholder="Nama Practitioner">
                                    <x-slot name="appendSlot">
                                        <x-adminlte-button id="btnPractitionerByName" theme="primary"
                                            label="Cari Practitioner" />
                                    </x-slot>
                                </x-adminlte-input>
                            </div>

                        </div>
                    </form>
                </x-adminlte-card>
            </div>
            <div class="col-12">
                <x-adminlte-card title="Hasil Pencarian Patient" theme="secondary" collapsible>
                    <table class="table table-bordered table-sm table-striped" id="tablePractitioner">
                        <thead>
                            <th>Identifier</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </x-adminlte-card>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)
@section('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#btnPatient').click(function() {
                $.LoadingOverlay("show");
                $('#modalPatient').modal('show');
                $.LoadingOverlay("hide");
            });
            $('#btnPatientByName').click(function() {
                $.LoadingOverlay("show");
                var birthdate = $('#birthdate').val();
                var gender = $('#gender').find(":selected").val();
                var name = $('#name').val();
                var data = $('#formPatientByName').serialize();
                $.ajax({
                    url: "{{ route('api.satusehat.patient_by_name') }}",
                    data: data,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#table1').find("tr:gt(0)").empty();
                        $.each(data.entry, function(key, value) {
                            if (value.resource.active) {
                                var status = "Aktif";
                            } else {
                                var status = "Non-aktif";
                            }
                            $('#table1').append('<tr><td>' + value.resource
                                .identifier[
                                    0]
                                .value + '</td><td>' + value.resource
                                .identifier[
                                    1]
                                .value + ' </td><td>' + value.resource.name[0]
                                .text + '</td><td>' + value.resource.gender +
                                '</td><td>' + status + '</td><td>' +
                                "<button class='btn btn-warning btn-xs btnPilihPatient' data-id=" +
                                value.resource.identifier[0].value +
                                ">Pilih</button> </td></tr>"
                            );
                        })
                        if (data.total > 0) {
                            swal.fire(
                                'Success',
                                'Ditemukan ' + data.total + ' Pasien',
                                'success'
                            );
                        } else {
                            swal.fire(
                                'Not Found',
                                'Ditemukan ' + data.total + ' Pasien',
                                'error'
                            );

                        }
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.data,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnPatientByNIK').click(function() {
                $.LoadingOverlay("show");
                var nik = $('#nik').val();
                if (nik == '') {
                    alert('Silahkan isi NIK terlebih dahulu !');
                } else {
                    $.ajax({
                        url: "{{ route('api.satusehat.patient_index') }}" + "/nik/" + nik,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            $('#table1').find("tr:gt(0)").empty();
                            $.each(data.entry, function(key, value) {
                                if (value.resource.active) {
                                    var status = "Aktif";
                                } else {
                                    var status = "Non-aktif";
                                }
                                $('#table1').append('<tr><td>' + value.resource
                                    .identifier[
                                        0]
                                    .value + '</td><td>' + value.resource
                                    .identifier[
                                        1]
                                    .value + ' </td><td>' + value.resource.name[0]
                                    .text + '</td><td>' + value.resource.gender +
                                    '</td><td>' + status + '</td><td>' +
                                    "<button class='btn btn-warning btn-xs btnPilihPatient' data-id=" +
                                    value.resource.identifier[0].value +
                                    ">Pilih</button> </td></tr>"
                                );
                            })
                            if (data.total > 0) {
                                swal.fire(
                                    'Success',
                                    'Ditemukan ' + data.total + ' Pasien',
                                    'success'
                                );
                            } else {
                                swal.fire(
                                    'Not Found',
                                    'Ditemukan ' + data.total + ' Pasien',
                                    'error'
                                );
                            }
                            $.LoadingOverlay("hide");
                        },
                        error: function(data) {
                            swal.fire(
                                'Error',
                                data.statusText + ' ' + data.status,
                                'error'
                            );
                            $.LoadingOverlay("hide");
                        }
                    });
                }
            });
            $('body').on('click', '.btnPilihPatient', function() {
                $.LoadingOverlay("show");
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('api.satusehat.patient_index') }}" + "/id/" + id,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#patient_id').val(data.id);
                        $('#patient_name').val(data.name[0].text);
                        $('#modalPatient').modal('hide');
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.data,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnPractitioner').click(function() {
                $.LoadingOverlay("show");
                $('#modalPractitioner').modal('show');
                $.LoadingOverlay("hide");
            });
            $('#btnPractitionerByName').click(function() {
                $.LoadingOverlay("show");
                var birthdate = $('#birthdate_practitioner').val();
                var gender = $('#gender_practitioner').find(":selected").val();
                var name = $('#name_practitioner').val();
                var data = "birthdate=" + birthdate + "&name=" + name + "&gender=" + gender;
                $.ajax({
                    url: "{{ route('api.satusehat.patient_by_name') }}",
                    data: data,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#table1').find("tr:gt(0)").empty();
                        $.each(data.entry, function(key, value) {
                            if (value.resource.active) {
                                var status = "Aktif";
                            } else {
                                var status = "Non-aktif";
                            }
                            $('#tablePractitioner').append('<tr><td>' + value.resource
                                .identifier[
                                    0]
                                .value + '</td><td>' + value.resource
                                .identifier[
                                    1]
                                .value + ' </td><td>' + value.resource.name[0]
                                .text + '</td><td>' + value.resource.gender +
                                '</td><td>' + status + '</td><td>' +
                                "<button class='btn btn-warning btn-xs btnPilihPractitioner' data-id=" +
                                value.resource.identifier[0].value +
                                ">Pilih</button> </td></tr>"
                            );
                        })
                        if (data.total > 0) {
                            swal.fire(
                                'Success',
                                'Ditemukan ' + data.total + ' Pasien',
                                'success'
                            );
                        } else {
                            swal.fire(
                                'Not Found',
                                'Ditemukan ' + data.total + ' Pasien',
                                'error'
                            );

                        }
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.data,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            $('#btnPractitionerByNIK').click(function() {
                $.LoadingOverlay("show");
                var nik = $('#nik_practitioner').val();
                if (nik == '') {
                    alert('Silahkan isi NIK terlebih dahulu !');
                    $.LoadingOverlay("hide");
                } else {
                    $.ajax({
                        url: "{{ route('api.satusehat.practitioner_index') }}" + "/nik/" + nik,
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            $('#tablePractitioner').find("tr:gt(0)").empty();
                            $.each(data.entry, function(key, value) {
                                if (value.resource.active) {
                                    var status = "Aktif";
                                } else {
                                    var status = "Non-aktif";
                                }
                                $('#tablePractitioner').append('<tr><td>' + value
                                    .resource
                                    .identifier[
                                        0]
                                    .value + '</td><td>' + value.resource
                                    .identifier[
                                        1]
                                    .value + ' </td><td>' + value.resource.name[0]
                                    .text + '</td><td>' + value.resource.gender +
                                    '</td><td>' + status + '</td><td>' +
                                    "<button class='btn btn-warning btn-xs btnPilihPractitioner' data-id=" +
                                    value.resource.identifier[0].value +
                                    ">Pilih</button> </td></tr>"
                                );
                            })
                            if (data.total > 0) {
                                swal.fire(
                                    'Success',
                                    'Ditemukan ' + data.total + ' Pasien',
                                    'success'
                                );
                            } else {
                                swal.fire(
                                    'Not Found',
                                    'Ditemukan ' + data.total + ' Pasien',
                                    'error'
                                );
                            }
                            $.LoadingOverlay("hide");
                        },
                        error: function(data) {
                            swal.fire(
                                'Error',
                                data.statusText + ' ' + data.status,
                                'error'
                            );
                            $.LoadingOverlay("hide");
                        }
                    });
                }
            });
            $('body').on('click', '.btnPilihPractitioner', function() {
                $.LoadingOverlay("show");
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('api.satusehat.practitioner_index') }}" + "/id/" + id,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        $('#practitioner_id').val(data.id);
                        $('#practitioner_name').val(data.name[0].text);
                        $('#modalPractitioner').modal('hide');
                        $.LoadingOverlay("hide");
                    },
                    error: function(data) {
                        swal.fire(
                            data.statusText + ' ' + data.status,
                            data.responseJSON.data,
                            'error'
                        );
                        $.LoadingOverlay("hide");
                    }
                });
            });
            // $('#btnStore').click(function(e) {
            //     $.LoadingOverlay("show");
            //     e.preventDefault();
            //     $.ajax({
            //         data: $('#formOrganization').serialize(),
            //         url: "{{ route('api.satusehat.location_store_api') }}",
            //         type: "POST",
            //         dataType: 'json',
            //         success: function(data) {
            //             swal.fire(
            //                 'Success',
            //                 'Data Berhasil Disimpan',
            //                 'success'
            //             ).then(okay => {
            //                 if (okay) {
            //                     $.LoadingOverlay("show");
            //                     location.reload();
            //                 }
            //             });
            //         },
            //         error: function(data) {
            //             console.log(data);
            //             swal.fire(
            //                 data.statusText + ' ' + data.status,
            //                 data.responseJSON.original.data,
            //                 'error'
            //             );
            //         }
            //     });
            //     $.LoadingOverlay("hide");
            // });
            // $('#btnUpdate').click(function(e) {
            //     $.LoadingOverlay("show");
            //     e.preventDefault();
            //     var id = $('#id').val();
            //     var url = "{{ route('api.satusehat.location_index') }}" + "/update/" + id;
            //     $.ajax({
            //         data: $('#formOrganization').serialize(),
            //         url: url,
            //         type: "PUT",
            //         dataType: 'json',
            //         success: function(data) {
            //             swal.fire(
            //                 'Success',
            //                 'Data Berhasil Disimpan',
            //                 'success'
            //             ).then(okay => {
            //                 if (okay) {
            //                     $.LoadingOverlay("show");
            //                     location.reload();
            //                 }
            //             });
            //         },
            //         error: function(data) {
            //             console.log(data);
            //             swal.fire(
            //                 data.statusText + ' ' + data.status,
            //                 data.responseJSON.original.data,
            //                 'error'
            //             );
            //         }
            //     });
            //     $.LoadingOverlay("hide");
            // });
            // $("#city").select2({
            //     theme: "bootstrap4",
            //     ajax: {
            //         url: "{{ route('get_city') }}",
            //         type: "get",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             code = $("#province").find(":selected").val();
            //             if (code.length != 0) {
            //                 return {
            //                     code: code,
            //                     search: params.term // search term
            //                 };
            //             } else {
            //                 return alert('Silahkan Pilih Provinsi Terlebih Dahulu !')
            //             }
            //         },
            //         processResults: function(response) {
            //             return {
            //                 results: response
            //             };
            //         },
            //         cache: true
            //     }
            // });
            // $("#district").select2({
            //     theme: "bootstrap4",
            //     ajax: {
            //         url: "{{ route('get_district') }}",
            //         type: "get",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             code = $("#city").find(":selected").val();
            //             if (code != null) {
            //                 return {
            //                     code: code,
            //                     search: params.term // search term
            //                 };
            //             } else {
            //                 return alert('Silahkan Pilih Kota / Kabupaten Terlebih Dahulu !')
            //             }
            //         },
            //         processResults: function(response) {
            //             return {
            //                 results: response
            //             };
            //         },
            //         cache: true
            //     }
            // });
            // $("#village").select2({
            //     theme: "bootstrap4",
            //     ajax: {
            //         url: "{{ route('get_village') }}",
            //         type: "get",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             code = $("#district").find(":selected").val();
            //             if (code != null) {
            //                 return {
            //                     code: code,
            //                     search: params.term // search term
            //                 };
            //             } else {
            //                 return alert('Silahkan Pilih Kecamatan Terlebih Dahulu !')
            //             }
            //         },
            //         processResults: function(response) {
            //             return {
            //                 results: response
            //             };
            //         },
            //         cache: true
            //     }
            // });
        });
    </script>
@endsection
