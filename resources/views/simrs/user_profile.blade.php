@extends('adminlte::page')

@section('title', 'Profil ' . $user->name)

@section('content_header')
    <h1>Profil {{ $user->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset('vendor/adminlte/dist/img/user.png') }}" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $user->name }}</h3>
                    <p class="text-muted text-center">
                        @if (!empty($user->getRoleNames()))
                            @foreach ($user->getRoleNames() as $v)
                                <label class="badge badge-primary"> {{ $v }}</label>
                            @endforeach
                        @endif
                    </p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Username</b><br>{{ $user->username }}<br>
                            <b>Email</b><br>{{ $user->email }}<br>
                            <b>Telepon</b><br>{{ $user->phone }}<br>
                        </li>

                    </ul>
                </div>
            </div>
            {{-- <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">About Me</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Education</strong>

                    <p class="text-muted">
                        B.S. in Computer Science from the University of Tennessee at Knoxville
                    </p>

                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                    <p class="text-muted">Malibu, California</p>

                    <hr>

                    <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                    <p class="text-muted">
                        <span class="tag tag-danger">UI Design</span>
                        <span class="tag tag-success">Coding</span>
                        <span class="tag tag-info">Javascript</span>
                        <span class="tag tag-warning">PHP</span>
                        <span class="tag tag-primary">Node.js</span>
                    </p>

                    <hr>

                    <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

                    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim
                        neque.</p>
                </div>
            </div> --}}
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        {{-- <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a>
                        </li> --}}
                        <li class="nav-item"><a class="nav-link active" href="#biodata" data-toggle="tab">Biodata</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a>
                        </li>
                    </ul>
                </div>
                {!! Form::model($user, ['method' => 'PATCH', 'route' => ['profil.update'], 'files' => true]) !!}
                <div class="card-body">
                    <div class="tab-content">
                        {{-- <div class="tab-pane" id="timeline">
                            <div class="timeline timeline-inverse">
                                <div class="time-label">
                                    <span class="bg-danger">
                                        10 Feb. 2014
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-envelope bg-primary"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 12:05</span>

                                        <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                                        <div class="timeline-body">
                                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                            weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                            jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                            quora plaxo ideeli hulu weebly balihoo...
                                        </div>
                                        <div class="timeline-footer">
                                            <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                            <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-user bg-info"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>

                                        <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your
                                            friend request
                                        </h3>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-comments bg-warning"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>

                                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                                        <div class="timeline-body">
                                            Take me to your leader!
                                            Switzerland is small and neutral!
                                            We are more like Germany, ambitious and misunderstood!
                                        </div>
                                        <div class="timeline-footer">
                                            <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="time-label">
                                    <span class="bg-success">
                                        3 Jan. 2014
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-camera bg-purple"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 2 days ago</span>
                                        <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                                    </div>
                                </div>
                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div> --}}
                        <div class="active tab-pane" id="biodata">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> Ada kesalahan input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik">NIK</label>
                                        {!! Form::text('nik', null, ['class' => 'form-control' . ($errors->has('nik') ? ' is-invalid' : ''), 'id' => 'nik', 'placeholder' => 'NIK', 'autofocus']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'id' => 'name', 'placeholder' => 'Nama', 'required']) !!}
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="tempat_lahir">Tempat Lahir</label>
                                            {!! Form::text('tempat_lahir', null, ['class' => 'form-control' . ($errors->has('tempat_lahir') ? ' is-invalid' : ''), 'id' => 'tempat_lahir', 'placeholder' => 'Tempat Lahir']) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="tanggal_lahir">Tanggal Lahir</label>
                                            {!! Form::date('tanggal_lahir', null, ['class' => 'form-control' . ($errors->has('tanggal_lahir') ? ' is-invalid' : ''), 'id' => 'tanggal_lahir', 'placeholder' => 'Tanggal Lahir']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        {!! Form::select('gender', $genders, null, ['class' => 'form-control' . ($errors->has('gender') ? ' is-invalid' : ''), 'id' => 'gender', 'placeholder' => 'Pilih Jenis Kelamin']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="agama">Agama</label>
                                        {!! Form::select('agama', $agamas, null, ['class' => 'form-control' . ($errors->has('agama') ? ' is-invalid' : ''), 'id' => 'agama', 'placeholder' => 'Pilih Agama']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="perkawinan">Status Perkawinan</label>
                                        {!! Form::select('perkawinan', $perkawinans, null, ['class' => 'form-control' . ($errors->has('perkawinan') ? ' is-invalid' : ''), 'id' => 'perkawinan', 'placeholder' => 'Pilih Pekerjaan']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="pekerjaan">Pekerjaan</label>
                                        {!! Form::text('pekerjaan', null, ['class' => 'form-control' . ($errors->has('pekerjaan') ? ' is-invalid' : ''), 'id' => 'pekerjaan', 'placeholder' => 'Pekerjaan']) !!}
                                        {{-- {!! Form::select('pekerjaan', $pekerjaans, null, ['class' => 'form-control' . ($errors->has('pekerjaan') ? ' is-invalid' : ''), 'id' => 'pekerjaan', 'placeholder' => 'Pilih Pekerjaan',]) !!} --}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="negara">Kenegaraan</label>
                                        {!! Form::text('negara', null, ['class' => 'form-control' . ($errors->has('negara') ? ' is-invalid' : ''), 'id' => 'negara', 'placeholder' => 'Kenegaraan']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="province_id">Provinsi</label>
                                        {!! Form::select('province_id', $provinces, null, ['class' => 'form-control' . ($errors->has('province_id') ? ' is-invalid' : ''), 'id' => 'province_id', 'placeholder' => 'Pilih Provinsi']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="city_id">Kabupaten / Kota</label>
                                        {!! Form::select('city_id', $roles, null, ['class' => 'form-control' . ($errors->has('city_id') ? ' is-invalid' : ''), 'id' => 'city_id', 'placeholder' => 'Pilih Kabupaten / Kota']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="district_id">Kecamatan</label>
                                        {!! Form::select('district_id', $roles, null, ['class' => 'form-control' . ($errors->has('district_id') ? ' is-invalid' : ''), 'id' => 'district_id', 'placeholder' => 'Pilih Kecamatan']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="village_id">Desa / Kelurahan</label>
                                        {!! Form::select('village_id', $roles, null, ['class' => 'form-control' . ($errors->has('village_id') ? ' is-invalid' : ''), 'id' => 'village_id', 'placeholder' => 'Pilih Desa / Kelurahan']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        {!! Form::textarea('alamat', null, ['class' => 'form-control' . ($errors->has('alamat') ? ' is-invalid' : ''), 'id' => 'alamat', 'rows' => 3, 'placeholder' => 'Pilih Provinsi']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> Ada kesalahan input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="phone">Nomor Telephone</label>
                                {!! Form::text('phone', null, ['class' => 'form-control' . ($errors->has('phone') ? ' is-invalid' : ''), 'id' => 'phone', 'placeholder' => 'Nomor Telephone']) !!}
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                {!! Form::email('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'Email', 'required']) !!}
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                {!! Form::text('username', null, ['class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : ''), 'id' => 'username', 'placeholder' => 'Username', 'required']) !!}
                            </div>
                            <div class="form-group">
                                <label for="username">Password</label><br>
                                Silahkan klik <a href="{{ route('password.request') }}">disini</a> jika ingin mereset
                                password, anda akan merubahnya melalui email yang akan dikirimkan.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-check float-left">
                        <input class="form-check-input" name="comfirmed" id="comfirmed" type="checkbox" required>
                        <label class="form-check-label" for="comfirmed">Data telah sesuai dan siap untuk disimpan</label>
                    </div>
                    <button type="submit" class="btn btn-success float-right">Simpan</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
