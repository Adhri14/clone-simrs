@extends('adminlte::page')

@section('title', 'Tambah Blog')

@section('content_header')
    <h1>Edit Blog Posting</h1>
@stop

@section('content')
    <div class="row">
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
            <x-adminlte-card title="Edit Blog {{ $data->title }}" theme="secondary">
                <form action="{{ route('blog.update', $data->id) }}" id="myform" method="post">
                    @csrf
                    @method('PUT')
                    <x-adminlte-input name="title" class="title" label="Judul" placeholder="Judul Postingan"
                        enable-old-support value="{{ old('title') ?? $data->title }}" />

                    <x-adminlte-select2 name="category_id" label="Kategori">
                        <option value="" selected disabled>Pilih Kategori</option>
                        @foreach ($categories as $item)
                            @if ($data->category_id === $item->id)
                                <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                            @else
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </x-adminlte-select2>

                    <x-adminlte-select2 name="status" label="Status">
                        <option value="" selected disabled>Pilih Status</option>
                        <option value="active" {{ $data->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-active" {{ $data->status === 'non-active' ? 'selected' : '' }}>Tidak Aktif</option>
                    </x-adminlte-select2>

                    <label for="body">Body</label>
                    <textarea class="ckeditor form-control" name="body" id="body">{{ $data->body ?? old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                    
                </form>
                <x-slot name="footerSlot">
                    <x-adminlte-button form="myform" type="submit" theme="success" label="Simpan" />
                    <x-adminlte-button theme="danger" label="Kembali"
                        onclick="window.location='{{ url()->previous() }}'" />
                </x-slot>
            </x-adminlte-card>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    
@stop

@section('plugins.Select2', true)