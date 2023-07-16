@extends('adminlte::page')

@section('title', 'Tambah Blog')

@section('content_header')
    <h1>Tambah Blog Posting</h1>
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
            <x-adminlte-card title="Tambah Blog" theme="secondary">
                <form action="{{ route('blog.store') }}" id="myform" method="post">
                    @csrf
                    <x-adminlte-input name="title" class="title" label="Judul" placeholder="Judul Postingan"
                        enable-old-support value="{{ old('title') }}" />
                    <x-adminlte-select2 name="category_id" label="Kategori">
                        <option value="" selected disabled>Pilih Kategori</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" @selected(old('category_id') === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    <x-adminlte-select2 name="status" label="Status">
                        <option value="" selected disabled>Pilih Status</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-active" {{ old('status') === 'non-active' ? 'selected' : '' }}>Tidak Aktif</option>
                    </x-adminlte-select2>

                    <label for="body">Body</label>
                    <textarea class="ckeditor form-control" name="body" id="body">{{ old('body') }}</textarea>
                    {{-- <input id="body" type="hidden" name="body">
                    <trix-editor class="@error('body') is-invalid @enderror" name="body" input="body"></trix-editor> --}}
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