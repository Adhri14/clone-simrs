@extends('adminlte::page')

@section('title', 'File Rekam Medis')

@section('content_header')
    <h1>File Rekam Medis</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Scan File Rekam Medis" theme="warning" collapsible>
                {{-- scanner localhost --}}
                {{-- <button class="btn btn-primary" type="button" onclick="scanToLocalDisk();">Scan</button>
                <div id="response"></div> --}}

                {{-- <x-adminlte-input name="Tanggal Kunjungan" label="Nama Pasien" /> --}}
                {{-- <x-adminlte-input name="Tanggal Kunjungan" label="Nama Pasien" /> --}}
                {{-- scanner upload --}}
                <form id="form1" action="http://localhost/scanner/upload.php?action=upload" method="POST"
                    enctype="multipart/form-data" target="_blank">
                    @php
                        $config = ['format' => 'YYYY-MM-DD'];
                    @endphp
                    <x-adminlte-input-date name="tanggal" label="Tanggal Antrian" :config="$config"
                        value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-input name="norm" label="No RM" />
                    <x-adminlte-input name="nama" label="Nama Pasien" />
                    <x-adminlte-input name="kode" label="Kode Kunjungan" />
                    <x-adminlte-input name="tipekunjungan" label="Tipe Kunjungan" />
                    <x-adminlte-input name="counter" label="Counter" />
                    <x-adminlte-input id="sample-field" name="sample-field" label="File RM" value="File RM" />
                    <button type="button" class="btn btn-primary" onclick="scanToPdfWithThumbnails();">Scan</button>
                    <input type="button" class="btn btn-success" value="Submit" onclick="submitFormWithScannedImages();">
                </form>
                <div id="server_response"></div>
                <div id="images"></div>
                {{-- image base64 --}}
                {{-- <img src="data:image/png;base64,{{ base64_encode($im_blob) }}" width="500" alt="Red dot" /> --}}
            </x-adminlte-card>
            <x-adminlte-card title="Data File Rekam Medis" theme="warning" collapsible>
                <iframe src="http://localhost/scanner/tmp/22101207511220390.pdf" width="100%"
                    height="500px">
                </iframe>
            </x-adminlte-card>

        </div>
        {{-- <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title">Ekko Lightbox</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <a href="https://via.placeholder.com/1200/FFFFFF.png?text=1" data-toggle="lightbox"
                                data-title="sample 1 - white" data-gallery="gallery">
                                <img src="data:image/png;base64,{{ base64_encode($im_blob) }}" class="img-fluid mb-2"
                                    alt="Red dot" />
                            </a>
                        </div>
                        <div class="col-sm-2">
                            <a href="https://via.placeholder.com/1200/FFFFFF.png?text=1" data-toggle="lightbox"
                                data-title="sample 1 - white" data-gallery="gallery">
                                <img src="https://via.placeholder.com/300/FFFFFF?text=1" class="img-fluid mb-2"
                                    alt="white sample" />
                            </a>
                        </div>
                        <div class="col-sm-2">
                            <a href="https://via.placeholder.com/1200/000000.png?text=2" data-toggle="lightbox"
                                data-title="sample 2 - black" data-gallery="gallery">
                                <img src="https://via.placeholder.com/300/000000?text=2" class="img-fluid mb-2"
                                    alt="black sample" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@stop

@section('plugins.EkkoLightBox', true)

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/scannerjs/scanner.css') }}">
@endsection
@section('js')
    {{-- <script src="https://cdn.asprise.com/scannerjs/scanner.js" type="text/javascript"></script> --}}
    <script src="{{ asset('vendor/scannerjs/scanner.js') }} " type="text/javascript"></script>
    {{-- localhost --}}
    <script>
        /** Initiates a scan */
        function scanToLocalDisk() {
            var storagePath = '{{ str_replace('\\', '\\\\', storage_path()) }}';
            scanner.scan(displayResponseOnPage, {
                "output_settings": [{
                    "type": "save",
                    "format": "jpg",
                    "save_path": storagePath + "\\${TMS}${EXT}"
                }]
            });
        }

        function displayResponseOnPage(successful, mesg, response) {
            if (!successful) { // On error
                document.getElementById('response').innerHTML = 'Failed: ' + mesg;
                return;
            }

            if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) { // User cancelled.
                document.getElementById('response').innerHTML = 'User cancelled';
                return;
            }

            document.getElementById('response').innerHTML = scanner.getSaveResponse(response);
        }
    </script>
    {{-- upload --}}
    <script>
        /** Scan: output PDF original and JPG thumbnails */
        function scanToPdfWithThumbnails() {
            scanner.scan(displayImagesOnPage, {
                "output_settings": [{
                        "type": "return-base64",
                        "format": "pdf",
                        "pdf_text_line": "By ${USERNAME} on ${DATETIME}"
                    },
                    {
                        "type": "return-base64-thumbnail",
                        "format": "png",
                        "thumbnail_height": 200
                    }
                ]
            });
        }

        /** Processes the scan result */
        function displayImagesOnPage(successful, mesg, response) {
            if (!successful) { // On error
                console.error('Failed: ' + mesg);
                return;
            }

            if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) { // User cancelled.
                console.info('User cancelled');
                return;
            }

            var scannedImages = scanner.getScannedImages(response, true, false); // returns an array of ScannedImage
            for (var i = 0;
                (scannedImages instanceof Array) && i < scannedImages.length; i++) {
                var scannedImage = scannedImages[i];
                processOriginal(scannedImage);
            }

            var thumbnails = scanner.getScannedImages(response, false, true); // returns an array of ScannedImage
            for (var i = 0;
                (thumbnails instanceof Array) && i < thumbnails.length; i++) {
                var thumbnail = thumbnails[i];
                processThumbnail(thumbnail);
            }
        }

        /** Images scanned so far. */
        var imagesScanned = [];

        /** Processes an original */
        function processOriginal(scannedImage) {
            imagesScanned.push(scannedImage);
        }

        /** Processes a thumbnail */
        function processThumbnail(scannedImage) {
            var elementImg = scanner.createDomElementFromModel({
                'name': 'img',
                'attributes': {
                    'class': 'scanned border border-primary m-1',
                    'src': scannedImage.src
                }
            });
            document.getElementById('images').appendChild(elementImg);
        }

        /** Upload scanned images by submitting the form */
        function submitFormWithScannedImages() {
            if (scanner.submitFormWithImages('form1', imagesScanned, function(xhr) {
                    console.log(xhr);
                    if (xhr.readyState == 4) { // 4: request finished and response is ready
                        alert('Berhasil Submit');
                        document.getElementById('server_response').innerHTML = "<h2>Berhasil Submit</h2>"
                        document.getElementById('images').innerHTML = ''; // clear images
                        imagesScanned = [];
                    } else {
                        alert('Berhasil Submit');
                        document.getElementById('server_response').innerHTML = "<h2>Response from the server: </h2>";
                    }
                })) {
                document.getElementById('server_response').innerHTML = "Submitting, please stand by ...";
            } else {
                document.getElementById('server_response').innerHTML = "Form submission cancelled. Please scan first.";
            }
        }
    </script>
    <script>
        $(function() {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });
        })
    </script>
@endsection

@section('plugins.TempusDominusBs4', true)
