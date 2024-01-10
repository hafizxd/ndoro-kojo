<x-app-layout>
    @section('title', 'Ternak')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">{{ ucwords(str_replace('-', ' ', $urlType)) }} {{ isset($livestockType) ? ucwords(strtolower($livestockType->livestock_type)) : '' }}</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
                </div>
            </div>

            <form action="#" class="my-3">
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
                                <select class="form-control" id="province" type="text" placeholder="Pilih provinsi">
                                    <option value="" selected>-- PILIH --</option>
                                    @foreach ($provinces as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="regency">Kabupaten / Kota</label>
                                <select class="form-control" id="regency" type="text" placeholder="Pilih Kabupaten / Kota">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="district">Kecamatan</label>
                                <select class="form-control" id="district" type="text" placeholder="Pilih Kecamatan">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="village">Kelurahan / Desa</label>
                                <select class="form-control" id="village" type="text" placeholder="Pilih Kelurahan / Desa">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-2">
                                <button class="btn btn-secondary" type="button" onClick="clearFilterRegion()">Clear</button>
                                <button class="btn btn-primary" type="button" onClick="filterRegion()">Filter</button>
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
                                <a target="_blank" href="{{ route('livestock.report.detail.export', [$urlType, isset($livestockType) ? $livestockType->id : 'all']) }}?daterange={{ isset($dateStart) ? $dateStart . ' to ' . $dateEnd : '' }}" class="btn btn-primary" type="button">Download</a>
                            </div>
                        </div>

                        <div class="table-responsive-sm scrollbar">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" style="min-width: 150px;">Peternak</th>
                                        <th scope="col" style="min-width: 150px;">Kode Ternak</th>
                                        <th scope="col" style="min-width: 150px;">Kandang</th>
                                        <th scope="col" style="min-width: 150px;">Pakan</th>
                                        <th scope="col" style="min-width: 150px;">Limbah</th>
                                        <th scope="col" style="min-width: 150px;">Umur</th>
                                        <th scope="col" style="min-width: 150px;">Jenis Kelamin</th>
                                        <th scope="col" style="min-width: 150px;">Status Ternak</th>
                                        <th scope="col" style="min-width: 150px;">Bulan</th>
                                        <th scope="col" style="min-width: 150px;">Tahun</th>
                                        <th scope="col" style="min-width: 150px;">Provinsi</th>
                                        <th scope="col" style="min-width: 150px;">Desa</th>
                                        <th scope="col" style="min-width: 150px;">Kecamatan</th>
                                        <th scope="col" style="min-width: 150px;">Kelurahan/Desa</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
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

            function filterDate() {
                $(".data-table").DataTable().ajax.reload();
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

            function filterRegion() {
                $(".data-table").DataTable().ajax.reload();
            }

            $(function() {

                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('livestock.report.detail', [$urlType, isset($livestockType) ? $livestockType->id : 'all']) }}",
                        type: 'GET',
                        data: function(d) {
                            d.daterange = '{{ isset($dateStart) ? $dateStart . ' to ' . $dateEnd : '' }}';
                            d.province_id = $('#province').val();
                            d.regency_id = $('#regency').val();
                            d.district_id = $('#district').val();
                            d.village_id = $('#village').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'farmer',
                            name: 'farmer'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'kandang',
                            name: 'kandang'
                        },
                        {
                            data: 'pakan',
                            name: 'pakan'
                        },
                        {
                            data: 'limbah',
                            name: 'limbah'
                        },
                        {
                            data: 'age',
                            name: 'age'
                        },
                        {
                            data: 'gender',
                            name: 'gender'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'month',
                            name: 'month'
                        },
                        {
                            data: 'year',
                            name: 'year'
                        },
                        {
                            data: 'province',
                            name: 'province'
                        },
                        {
                            data: 'regency',
                            name: 'regency'
                        },
                        {
                            data: 'district',
                            name: 'district'
                        },
                        {
                            data: 'village',
                            name: 'village'
                        }
                    ]
                });
            });

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
