@extends('adminlte::page')

@section('title', 'Profil ' . $user->name)

@section('content_header')
    <h1>Profil {{ $user->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-profile-widget name="{{ $user->name }}" desc="{{ $user->email }}" theme="primary"
                img="{{ $user->adminlte_image() }}">
                <ul class="nav flex-column col-md-12">
                    <li class="nav-item">
                        <b class="nav-link">Nama <b class="float-right ">{{ $user->name }}</b></b>
                    </li>
                    <li class="nav-item">
                        <b class="nav-link">Username <b class="float-right ">{{ $user->username }}</b></b>
                    </li>
                    <li class="nav-item">
                        <b class="nav-link">Phone <b class="float-right ">{{ $user->phone }}</b></b>
                    </li>
                    <li class="nav-item">
                        <b class="nav-link">Email <b class="float-right ">{{ $user->email }}</b></b>
                    </li>

                    <li class="nav-item">
                        <b class="nav-link">Role <b class="float-right ">
                                @foreach ($user->roles as $role)
                                    {{ $role->name }}
                                @endforeach

                            </b></b>
                    </li>
                </ul>

            </x-adminlte-profile-widget>
        </div>
        <div class="col-md-8">
        </div>
    </div>
@stop
