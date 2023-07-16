@extends('adminlte::page')

@section('title', 'Blog')

@section('content_header')
    <h1>Blog</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($message = Session::get('success'))
                <x-adminlte-alert title="Berhasil" theme="success" dismissable>
                    {{ $message }}
                </x-adminlte-alert>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Tabel Data Post" theme="secondary" collapsible>
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-button label="Tambah" class="btn-sm" theme="success" title="Tambah User"
                            icon="fas fa-plus" onclick="window.location='{{ route('blog.create') }}'" />
                        <x-adminlte-button label="Refresh" class="btn-sm" theme="warning" title="Refresh User"
                            icon="fas fa-sync" onclick="window.location='{{ route('blog.index') }}'" />
                    </div>
                    <div class="col-md-4">
                        <form action="" method="get">
                            <x-adminlte-input name="search" placeholder="Pencarian Judul" igroup-size="sm">
                                {{-- value="{{ $request->search }}"> --}}
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
                    $heads = ['Judul', 'slug', 'Kategori', 'Created_at','Action'];
                    $config['paging'] = false;
                    $config['lengthMenu'] = false;
                    $config['searching'] = false;
                    $config['info'] = false;
                    $config['responsive'] = true;
                @endphp
                <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" hoverable bordered compressed>
                    @foreach ($posts as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->slug }}</td>
                            <td>{{ $item->Category->name }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                <form action="{{ route('blog.update.status', $item->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PUT')
                                    <x-adminlte-button class="btn-xs" theme="success" icon="fas fa-check" type="submit" onclick="return confirm('Apakah anda ingin mengupdate status {{ $item->title }} ?')" />
                                </form>
                                <x-adminlte-button class="btn-xs" theme="warning" icon="fas fa-edit"
                                title="Edit User {{ $item->title }}"
                                onclick="window.location='{{ route('user.edit', $item) }}'" />
                                <form action="{{ route('blog.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <x-adminlte-button class="btn-xs" theme="danger" icon="fas fa-trash-alt" type="submit"
                                        onclick="return confirm('Apakah anda akan menghapus {{ $item->title }} ?')" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                <div class="text-info float-left ">
                    Data yang ditampilkan {{ $posts->count() }} dari total {{ $post_count }}
                </div>
                <div class="float-right pagination-sm">
                    {{ $posts->appends(request()->input())->links() }}
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
