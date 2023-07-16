@extends('vendor.medilab.master')

@section('title', 'SIRAMAH-RS Waled')

@section('content')
    <div class="container">
        <section class="mt-5 pt-5">
            <div class="row mt-5">
                <div class="col-lg-8">
                    {!! $data->body !!}
                </div>
            </div>
        </section>
    </div>
@endsection