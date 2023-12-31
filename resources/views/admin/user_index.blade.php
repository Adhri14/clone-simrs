@extends('adminlte::page')

@section('title', 'User Access')

@section('content_header')
    <h1>User Access</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-adminlte-small-box title="{{ $users_total }}" text="User Terdaftar" theme="success" icon="fas fa-users" />
        </div>
        <div class="col-12">
            @if ($errors->any())
                <x-adminlte-alert title="Ops Terjadi Masalah !" theme="danger" dismissable>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-adminlte-alert>
            @endif
            <x-adminlte-card title="Tabel Data User" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-button label="Tambah" class="btn-sm" theme="success" title="Tambah User"
                            icon="fas fa-plus" data-toggle="modal" data-target="#createPasien" />
                        <x-adminlte-button label="Refresh" class="btn-sm" theme="warning" title="Refresh User"
                            icon="fas fa-sync" onclick="window.location='{{ route('user.index') }}'" />
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('user.index') }}" method="get">
                            <x-adminlte-input name="search" placeholder="Pencarian Nama" igroup-size="sm"
                                value="{{ $request->search }}">
                                <x-slot name="appendSlot">
                                    <x-adminlte-button type="submit" theme="primary" label="Cari!" />
                                </x-slot>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text text-primary">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </form>
                    </div>
                </div>
                @php
                    $heads = ['ID', 'Name', 'Username', 'Email', 'Phone', 'Role', 'Verify', 'Created_at','Action'];
                    $config['paging'] = false;
                    $config['lengthMenu'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
                    $config['responsive'] = true;
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" hoverable bordered compressed>
                    @foreach ($users as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->username }}</td>
                            <td>
                                @if ($item->email_verified_at == null)
                                    <i class="fas fa-user-times text-danger" data-toggle="tooltip"
                                        title="Email User Belum Terverifikasi"></i>
                                @else
                                    <i class="fas fa-user-check text-success" data-toggle="tooltip"
                                        title="Email User Terverifikasi"></i>
                                @endif
                                {{ $item->email }}
                            </td>
                            <td>{{ $item->phone }}</td>
                            <td>
                                @foreach ($item->roles as $role)
                                    <span class="badge bg-success">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @isset($item->verificator)
                                    {{ $item->verificator->name }}
                                @endisset
                            </td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                <form action="{{ route('user.destroy', $item) }}" method="POST">
                                    <x-adminlte-button class="btn-xs" theme="success" icon="fas fa-check"
                                        title="Edit User {{ $item->name }}"
                                        onclick="window.location='{{ route('user_verifikasi', $item) }}'" />
                                    <x-adminlte-button class="btn-xs" theme="warning" icon="fas fa-edit"
                                        title="Edit User {{ $item->name }}"
                                        onclick="window.location='{{ route('user.edit', $item) }}'" />
                                    @csrf
                                    @method('DELETE')
                                    <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt" type="submit"
                                        onclick="return confirm('Apakah anda akan menghapus {{ $item->name }} ?')" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <div class="text-info float-left ">
                    Data yang ditampilkan {{ $users->count() }} dari total {{ $users_total }}
                </div>
                <div class="float-right pagination-sm">
                    {{ $users->appends(request()->input())->links() }}
                </div>
            </x-adminlte-card>
        </div>
    </div>
    <x-adminlte-modal id="createPasien" title="Tambah User" theme="success" v-centered static-backdrop scrollable>
        <form action="{{ route('user.store') }}" id="formTambahUser" method="POST">
            @csrf
            <x-adminlte-input name="name" label="Nama" placeholder="Nama Lengkap" enable-old-support required />
            <x-adminlte-select2 name="role" label="Role / Jabatan" enable-old-support required>
                <option value="" selected disabled>Pilih Role / Jabatan</option>
                @foreach ($roles as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </x-adminlte-select2>
            <x-adminlte-input name="phone" type="number" label="Nomor HP / Telepon"
                placeholder="Nomor HP / Telepon yang dapat dihubungi" enable-old-support />
            <x-adminlte-input name="email" type="email" label="Email" placeholder="Email" enable-old-support
                required />
            <x-adminlte-input name="username" label="Username" placeholder="Username" enable-old-support required />
            <x-adminlte-input name="password" type="password" label="Password" placeholder="Password" required />
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button form="formTambahUser" class="mr-auto" type="submit" theme="success" label="Simpan" />
            <x-adminlte-button theme="danger" label="Kembali" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
