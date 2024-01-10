<x-app-layout>
    @section('title', 'Report Ternak')

    @push('style')
        <style>
            .hiddenRow {
                padding: 0 !important;
            }

            .table tr td {
                padding-top: .8rem;
                padding-bottom: .8rem;
            }
        </style>
    @endpush

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Report Ternak</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
                </div>
            </div>

            <form action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <input type="hidden" name="daterange">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="timepicker2">Select Time Range</label>
                                    <input name="daterange" class="form-control datetimepicker flatpickr-input" id="timepicker2" type="text" placeholder="d-m-y to d-m-y" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-3">
                                <button class="btn btn-secondary" type="button" onClick="clearFilterDate()">Clear</button>
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="province">Provinsi</label>
                                <select class="form-control" id="province" name="province_id" type="text" placeholder="Pilih provinsi">
                                    <option value="" selected>-- PILIH --</option>
                                    @foreach ($provinces as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="regency">Kabupaten / Kota</label>
                                <select class="form-control" id="regency" name="regency_id" type="text" placeholder="Pilih Kabupaten / Kota">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="district">Kecamatan</label>
                                <select class="form-control" id="district" name="district_id" type="text" placeholder="Pilih Kecamatan">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="village">Kelurahan / Desa</label>
                                <select class="form-control" id="village" name="village_id" type="text" placeholder="Pilih Kelurahan / Desa">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-2">
                                <button class="btn btn-secondary" type="button" onClick="clearFilterRegion()">Clear</button>
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" width="1%"></th>
                                    <th scope="col" width="5%">Ternak</th>
                                    <th scope="col" width="10%">Total Ternak</th>
                                    <th scope="col" width="10%">Transaksi Jual Beli</th>
                                    <th scope="col" width="10%">Sedang Dijual</th>
                                    <th scope="col" width="10%">Lahir</th>
                                    <th scope="col" width="10%">Mati</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalTotalTernak = 0;
                                    $totalTransaksiJualBeli = 0;
                                    $totalSedangDijual = 0;
                                    $totalLahir = 0;
                                    $totalMati = 0;
                                @endphp
                                @foreach ($livestockTypes as $key => $value)
                                    <tr>
                                        <td>
                                            @if (isset($value->children) && count($value->children) > 1)
                                                <button class="btn btn-default btn-xs p-0 m-0" style="width: 50px;" data-bs-toggle="collapse" data-bs-target="#demo{{ $key }}" aria-expanded="false" aria-controls="demo{{ $key }}"><span class="fas fa-eye"></span></button>
                                            @endif
                                        </td>
                                        <td>{{ $value->livestock_type }}</td>
                                        <td><a href="{{ route('livestock.report.detail', ['total-ternak', $value->id]) }}">{{ $value->total_ternak }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['transaksi-jual-beli', $value->id]) }}">{{ $value->transaksi_jual_beli }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['sedang-dijual', $value->id]) }}">{{ $value->sedang_dijual }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['lahir', $value->id]) }}">{{ $value->lahir }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['mati', $value->id]) }}">{{ $value->mati }}</a></td>
                                    </tr>

                                    <tr>
                                        <td colspan="7" class="hiddenRow">
                                            <div class="collapse" id="demo{{ $key }}">
                                                <table class="table">
                                                    <tbody>
                                                        @foreach ($value->children as $child)
                                                            <tr>
                                                                <td width="1%">
                                                                    <button class="btn btn-default btn-xs p-0 m-0" style="width: 50px; visibility: hidden;"><span class="fas fa-eye"></span></button>
                                                                </td>
                                                                <td width="5%">{{ $child->livestock_type }}</td>
                                                                <td width="10%">{{ $child->total_ternak }}</td>
                                                                <td width="10%">{{ $child->transaksi_jual_beli }}</td>
                                                                <td width="10%">{{ $child->sedang_dijual }}</td>
                                                                <td width="10%">{{ $child->lahir }}</td>
                                                                <td width="10%">{{ $child->mati }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                    @php
                                        $totalTotalTernak += $value->total_ternak;
                                        $totalTransaksiJualBeli += $value->transaksi_jual_beli;
                                        $totalSedangDijual += $value->sedang_dijual;
                                        $totalLahir += $value->lahir;
                                        $totalMati += $value->mati;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td><b>TOTAL</b></td>
                                    <td><a href="{{ route('livestock.report.detail', ['total-ternak', 'all']) }}">{{ $totalTotalTernak }}</a></td>
                                    <td><a href="{{ route('livestock.report.detail', ['transaksi-jual-beli', 'all']) }}">{{ $totalTransaksiJualBeli }}</a></td>
                                    <td><a href="{{ route('livestock.report.detail', ['sedang-dijual', 'all']) }}">{{ $totalSedangDijual }}</a></td>
                                    <td><a href="{{ route('livestock.report.detail', ['lahir', 'all']) }}">{{ $totalLahir }}</a></td>
                                    <td><a href="{{ route('livestock.report.detail', ['mati', 'all']) }}">{{ $totalMati }}</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            function clearFilterDate() {
                document.getElementById('timepicker2').flatpickr().clear();
            }

            function clearFilterRegion() {
                $('#village').val('')
                $('#village').html('')
                $('#district').val('')
                $('#district').html('')
                $('#regency').val('')
                $('#regency').html('')
                $('#province').val('')
            }

            $(document).ready(function() {
                $('#province').change(function() {
                    createOverlay("Proses...");

                    var provinceId = $('#province').val();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('reference.regency') }}",
                        dataType: 'json',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'province_id': provinceId
                        },
                        success: function(data) {
                            gOverlay.hide();
                            console.log(data);

                            if (data.success) {
                                let options = "<option value='' disabled selected>-- PILIH --</option>";
                                data.payload.forEach(item => {
                                    options += "<option value='" + item.id + "'>" + item.name + "</option>"
                                });

                                $('#regency').html(options);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function(error) {
                            gOverlay.hide();
                            toastr.error("Network/server error\r\n" + error);
                        }
                    });
                });

                $('#regency').change(function() {
                    createOverlay("Proses...");

                    var regencyId = $('#regency').val();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('reference.district') }}",
                        dataType: 'json',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'regency_id': regencyId
                        },
                        success: function(data) {
                            gOverlay.hide();
                            console.log(data);

                            if (data.success) {
                                let options = "<option value='' disabled selected>-- PILIH --</option>";
                                data.payload.forEach(item => {
                                    options += "<option value='" + item.id + "'>" + item.name + "</option>"
                                });

                                $('#district').html(options);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function(error) {
                            gOverlay.hide();
                            toastr.error("Network/server error\r\n" + error);
                        }
                    });
                });

                $('#district').change(function() {
                    createOverlay("Proses...");

                    var districtId = $('#district').val();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('reference.village') }}",
                        dataType: 'json',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'district_id': districtId
                        },
                        success: function(data) {
                            gOverlay.hide();
                            console.log(data);

                            if (data.success) {
                                let options = "<option value='' disabled selected>-- PILIH --</option>";
                                data.payload.forEach(item => {
                                    options += "<option value='" + item.id + "'>" + item.name + "</option>"
                                });

                                $('#village').html(options);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function(error) {
                            gOverlay.hide();
                            toastr.error("Network/server error\r\n" + error);
                        }
                    });
                });

                $('#timepicker2').flatpickr({
                    mode: "range",
                    dateFormat: "d/m/Y",
                    disableMobile: true,
                    @if (isset($dateStart) && isset($dateEnd))
                        defaultDate: ["{{ $dateStart }}", "{{ $dateEnd }}"]
                    @endif
                });
            });
        </script>
    @endpush
</x-app-layout>
