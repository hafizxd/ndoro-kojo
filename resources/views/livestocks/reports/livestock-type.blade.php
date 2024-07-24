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
                        {{-- <div class="row">
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
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="timepicker2">Tahun</label>
                                <select id="year" name="year" class="form-control">
                                    <option value="" selected>-- PILIH --</option>
                                    @php $year = date('Y'); @endphp
                                    @for($i = 0; $i < 10; $i++)
                                        <option value="{{ $year-$i }}" @if(request()->query('year') == $year-$i) selected @endif>{{ $year-$i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="timepicker2">Dari Bulan</label>
                                <select id="from_month" name="from_month" class="form-control">
                                    <option value="" selected>-- PILIH --</option>
                                    <option value="01" @if(request()->query('from_month') == "01") selected @endif>JANUARI</option>
                                    <option value="02" @if(request()->query('from_month') == "02") selected @endif>FEBRUARI</option>
                                    <option value="03" @if(request()->query('from_month') == "03") selected @endif>MARET</option>
                                    <option value="04" @if(request()->query('from_month') == "04") selected @endif>APRIL</option>
                                    <option value="05" @if(request()->query('from_month') == "05") selected @endif>MEI</option>
                                    <option value="06" @if(request()->query('from_month') == "06") selected @endif>JUNI</option>
                                    <option value="07" @if(request()->query('from_month') == "07") selected @endif>JULI</option>
                                    <option value="08" @if(request()->query('from_month') == "08") selected @endif>AGUSTUS</option>
                                    <option value="09" @if(request()->query('from_month') == "09") selected @endif>SEPTEMBER</option>
                                    <option value="10" @if(request()->query('from_month') == "10") selected @endif>OKTOBER</option>
                                    <option value="11" @if(request()->query('from_month') == "11") selected @endif>NOVEMBER</option>
                                    <option value="12" @if(request()->query('from_month') == "12") selected @endif>DESEMBER</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="timepicker2">Sampai Bulan</label>
                                <select id="to_month" name="to_month" class="form-control">
                                    <option value="" selected>-- PILIH --</option>
                                    <option value="01" @if(request()->query('to_month') == "01") selected @endif>JANUARI</option>
                                    <option value="02" @if(request()->query('to_month') == "02") selected @endif>FEBRUARI</option>
                                    <option value="03" @if(request()->query('to_month') == "03") selected @endif>MARET</option>
                                    <option value="04" @if(request()->query('to_month') == "04") selected @endif>APRIL</option>
                                    <option value="05" @if(request()->query('to_month') == "05") selected @endif>MEI</option>
                                    <option value="06" @if(request()->query('to_month') == "06") selected @endif>JUNI</option>
                                    <option value="07" @if(request()->query('to_month') == "07") selected @endif>JULI</option>
                                    <option value="08" @if(request()->query('to_month') == "08") selected @endif>AGUSTUS</option>
                                    <option value="09" @if(request()->query('to_month') == "09") selected @endif>SEPTEMBER</option>
                                    <option value="10" @if(request()->query('to_month') == "10") selected @endif>OKTOBER</option>
                                    <option value="11" @if(request()->query('to_month') == "11") selected @endif>NOVEMBER</option>
                                    <option value="12" @if(request()->query('to_month') == "12") selected @endif>DESEMBER</option>
                                </select>
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
                                        <option value="{{ $value->id }}" @if(request()->query('province_id') == $value->id) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="regency">Kabupaten / Kota</label>
                                <select class="form-control" id="regency" name="regency_id" type="text" placeholder="Pilih Kabupaten / Kota">
                                    <option value="{{ $selectedRegency->id ?? '' }}">{{ $selectedRegency?->name ?? '' }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="district">Kecamatan</label>
                                <select class="form-control" id="district" name="district_id" type="text" placeholder="Pilih Kecamatan">
                                    <option value="{{ $selectedDistrict->id ?? '' }}">{{ $selectedDistrict?->name ?? '' }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="village">Kelurahan / Desa</label>
                                <select class="form-control" id="village" name="village_id" type="text" placeholder="Pilih Kelurahan / Desa">
                                    <option value="{{ $selectedVillage->id ?? '' }}">{{ $selectedVillage?->name ?? '' }}</option>
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
                        <div class="d-flex mb-5">
                            <div class="justify-content-end">
                                @php 
                                    $isMutasiDisabled = true;
                                    if (request()->query('year') && request()->query('from_month')) {
                                        $isMutasiDisabled = false;
                                    }
                                @endphp

                                <a target="_blank" href="{{ route('livestock.report.mutation.export', [
                                    'year' => request()->query('year'), 
                                    'from_month' => request()->query('from_month'), 
                                    'to_month' => request()->query('to_month'), 
                                    'province_id' => request()->query('province_id'), 
                                    'regency_id' => request()->query('regency_id'), 
                                    'district_id' => request()->query('district_id'), 
                                    'village_id' => request()->query('village_id') 
                                ]) }}" class="btn btn-primary {{ $isMutasiDisabled ? 'disabled' : '' }}" type="button">Download Mutasi</a>

                                {{-- <a target="_blank" href="{{ route('livestock.report.dead.export') }}" class="btn btn-primary" type="button">Download Penyebab Kematian</a> --}}
                            </div>
                        </div>

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
                $('#year').val('')
                $('#from_month').val('')
                $('#to_month').val('')
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
