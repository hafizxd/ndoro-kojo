<x-app-layout>
    @section('title', 'Ternak')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack ms-n1 me-2"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="text-primary position-relative fw-bold mb-0"><span class="bg-soft pe-2">Ternak</span><span class="border-primary-200 position-absolute top-50 translate-middle-y w-100 z-index--1 start-0 border"></span></h3>
                </div>
            </div>

            <div class="card border-300 my-5 border shadow-none" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div class="d-flex mb-5">
                            <div class="justify-content-end">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal2">Tambah Kandang + Ternak</button>
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Ternak</button>
                            </div>
                        </div>

                        <div class="table-responsive scrollbar">
                            <table class="data-table table">
                                <thead>
                                    <tr>
                                        <th scope="col" width="5%">#</th>
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
                                        <th scope="col" style="min-width: 250px;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-xl fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Ternak</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="POST">
                            <div class="row flex-center">
                                <div class="col-sm-12">
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Peternak - Kandang</label>
                                        <div class="form-icon-container">
                                            <select class="select form-control" id="kandang_id">
                                                <option value="" disabled selected>Pilih Peternak - Kandang</option>
                                                @foreach ($farmers as $farmer)
                                                    @foreach ($farmer->kandangs as $value)
                                                        <option value="{{ $value->id }}">{{ $farmer->fullname ?? $farmer->code }} - {{ $value->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="nominal">Jumlah</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="nominal" type="number" placeholder="Jumlah Ternak" value="1" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tipe Ternak</label>
                                        <div class="form-icon-container">
                                            <select id="livestock_type_id" class="form-control">
                                                @foreach ($livestockTypes as $value)
                                                    <option value="{{ $value->id }}">
                                                        @if ($value->level == 2)
                                                            {{ $value->livestockParent?->livestock_type . ' - ' }}
                                                        @endif {{ $value->livestock_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Pakan</label>
                                        <div class="form-icon-container">
                                            <select multiple class="form-select pakan" size="1" id="pakan" required="required"></select>
                                            <div class="invalid-feedback">Pilih minimal satu pakan</div>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Limbah</label>
                                        <div class="form-icon-container">
                                            <select id="limbah_id" class="form-control">
                                                @foreach ($limbah as $value)
                                                    <option value="{{ $value->id }}">{{ $value->pengolahan_limbah }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Umur</label>
                                        <div class="form-icon-container">
                                            <select id="age" class="form-control">
                                                <option value="ANAK">ANAK</option>
                                                <option value="MUDA">MUDA</option>
                                                <option value="DEWASA">DEWASA</option>
                                                <option value="BIBIT">BIBIT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Jenis Kelamin</label>
                                        <div class="form-icon-container">
                                            <select id="gender" class="form-control">
                                                <option value="JANTAN">JANTAN</option>
                                                <option value="BETINA">BETINA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Bulan</label>
                                        <div class="form-icon-container">
                                            <select id="month" class="form-control">
                                                <option value="01">JANUARI</option>
                                                <option value="02">FEBRUARI</option>
                                                <option value="03">MARET</option>
                                                <option value="04">APRIL</option>
                                                <option value="05">MEI</option>
                                                <option value="06">JUNI</option>
                                                <option value="07">JULI</option>
                                                <option value="08">AGUSTUS</option>
                                                <option value="09">SEPTEMBER</option>
                                                <option value="10">OKTOBER</option>
                                                <option value="11">NOVEMBER</option>
                                                <option value="12">DESEMBER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tahun</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="year" type="number" placeholder="Tahun" min="1000" max="9999" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onClick="storeData()">Save</button>
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-xl fade" id="createModal2" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Kandang + Ternak</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="POST">
                            <div class="row flex-center">
                                <div class="col-sm-12">
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Peternak</label>
                                        <div class="form-icon-container">
                                            <select class="select form-control" id="farmer_id">
                                                <option value="" disabled selected>Pilih Peternak</option>
                                                @foreach ($farmers as $value)
                                                    <option value="{{ $value->id }}">{{ $value->fullname ?? $value->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <hr>

                                    <label for=""><b>Kandang</b></label>

                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="name">Nama</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="name" type="text" placeholder="Nama Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tipe Ternak</label>
                                        <div class="form-icon-container">
                                            <select class="select form-control" id="kandang_type_id">
                                                <option value="" disabled selected>Pilih Tipe Ternak Kandang</option>
                                                @foreach ($livestockTypesParents as $value)
                                                    <option value="{{ $value->id }}">{{ $value->livestock_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="panjang">Panjang (m)</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="panjang" type="number" placeholder="Panjang Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="lebar">Lebar (m)</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="lebar" type="number" placeholder="Lebar Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="luas">Luas (m)</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="luas" type="number" placeholder="Luas Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="jenis">Jenis</label>
                                        <div class="form-icon-container">
                                            <select id="jenis" class="form-control">
                                                <option value="KANDANG BESAR">KANDANG BESAR</option>
                                                <option value="KANDANG MEDIUM">KANDANG MEDIUM</option>
                                                <option value="KANDANG KECIL">KANDANG KECIL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Provinsi</label>
                                        <div class="form-icon-container">
                                            <select id="province_id" class="form-control select">
                                                <option value="" disabled selected>-- Pilih Provinsi --</option>
                                                @foreach ($provinces as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Kabupaten / Kota</label>
                                        <div class="form-icon-container">
                                            <select id="regency_id" class="form-control select"></select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Kecamatan</label>
                                        <div class="form-icon-container">
                                            <select id="district_id" class="form-control select"></select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Desa</label>
                                        <div class="form-icon-container">
                                            <select id="village_id" class="form-control select"></select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="address">Alamat</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="address" type="text" placeholder="Alamat Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="rt_rw">RT & RW</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="rt_rw" type="text" placeholder="RT & RW Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="longitude">Longitude</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="longitude" type="text" placeholder="Longitude Kandang" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="latitude">Latitude</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="latitude" type="text" placeholder="Latitude Kandang" />
                                        </div>
                                    </div>

                                    <hr>
                                    <label for=""><b>Ternak</b></label>

                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="nominal">Jumlah</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="nominal" type="number" placeholder="Jumlah Ternak" value="1" />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tipe Ternak</label>
                                        <div class="form-icon-container">
                                            <select id="livestock_type_id" class="form-control">
                                                @foreach ($livestockTypes as $value)
                                                    <option value="{{ $value->id }}">
                                                        @if ($value->level == 2)
                                                            {{ $value->livestockParent?->livestock_type . ' - ' }}
                                                        @endif {{ $value->livestock_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Pakan</label>
                                        <div class="form-icon-container">
                                            <select multiple class="form-select pakan" size="1" id="pakan" required="required"></select>
                                            <div class="invalid-feedback">Pilih minimal satu pakan</div>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Limbah</label>
                                        <div class="form-icon-container">
                                            <select id="limbah_id" class="form-control">
                                                @foreach ($limbah as $value)
                                                    <option value="{{ $value->id }}">{{ $value->pengolahan_limbah }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Umur</label>
                                        <div class="form-icon-container">
                                            <select id="age" class="form-control">
                                                <option value="ANAK">ANAK</option>
                                                <option value="MUDA">MUDA</option>
                                                <option value="DEWASA">DEWASA</option>
                                                <option value="BIBIT">BIBIT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Jenis Kelamin</label>
                                        <div class="form-icon-container">
                                            <select id="gender" class="form-control">
                                                <option value="JANTAN">JANTAN</option>
                                                <option value="BETINA">BETINA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Bulan</label>
                                        <div class="form-icon-container">
                                            <select id="month" class="form-control">
                                                <option value="01">JANUARI</option>
                                                <option value="02">FEBRUARI</option>
                                                <option value="03">MARET</option>
                                                <option value="04">APRIL</option>
                                                <option value="05">MEI</option>
                                                <option value="06">JUNI</option>
                                                <option value="07">JULI</option>
                                                <option value="08">AGUSTUS</option>
                                                <option value="09">SEPTEMBER</option>
                                                <option value="10">OKTOBER</option>
                                                <option value="11">NOVEMBER</option>
                                                <option value="12">DESEMBER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tahun</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="year" type="number" placeholder="Tahun" min="1000" max="9999" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onClick="storeData()">Save</button>
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-lg fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Ternak</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="POST">
                            <input type="hidden" id="id_edit">

                            <div class="row flex-center">
                                <div class="col-sm-12">
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Kode Ternak</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="code_edit" type="text" placeholder="Kode Ternak" readonly />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Pakan</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="pakan_holder_edit" type="text" placeholder="Pakan" readonly />
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Update Pakan</label>
                                        <div class="form-icon-container">
                                            <select multiple class="form-select pakan_edit" size="1" id="pakan_edit" required="required"></select>
                                            <div class="invalid-feedback">Pilih minimal satu pakan</div>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Limbah</label>
                                        <div class="form-icon-container">
                                            <select id="limbah_edit" class="form-control">
                                                @foreach ($limbah as $value)
                                                    <option value="{{ $value->id }}">{{ $value->pengolahan_limbah }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Umur</label>
                                        <div class="form-icon-container">
                                            <select id="age_edit" class="form-control">
                                                <option value="ANAK">ANAK</option>
                                                <option value="MUDA">MUDA</option>
                                                <option value="DEWASA">DEWASA</option>
                                                <option value="BIBIT">BIBIT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Jenis Kelamin</label>
                                        <div class="form-icon-container">
                                            <select id="gender_edit" class="form-control">
                                                <option value="JANTAN">JANTAN</option>
                                                <option value="BETINA">BETINA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Bulan</label>
                                        <div class="form-icon-container">
                                            <select id="month_edit" class="form-control">
                                                <option value="01">JANUARI</option>
                                                <option value="02">FEBRUARI</option>
                                                <option value="03">MARET</option>
                                                <option value="04">APRIL</option>
                                                <option value="05">MEI</option>
                                                <option value="06">JUNI</option>
                                                <option value="07">JULI</option>
                                                <option value="08">AGUSTUS</option>
                                                <option value="09">SEPTEMBER</option>
                                                <option value="10">OKTOBER</option>
                                                <option value="11">NOVEMBER</option>
                                                <option value="12">DESEMBER</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tahun</label>
                                        <div class="form-icon-container">
                                            <input class="form-control" id="year_edit" type="number" placeholder="Tahun" min="1000" max="9999" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" onClick="updateData()">Save</button>
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            var createModalId = '';

            $("#createModal").on("shown.bs.modal", function(e) {
                createModalId = $(this).attr('id');

                let pakanOpt = [{
                    value: 'FERMENTASI',
                    label: 'FERMENTASI',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'KONSETRAT',
                    label: 'KONSETRAT',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'COMPLETE FEED',
                    label: 'COMPLETE FEED',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'RUMPUT',
                    label: 'RUMPUT',
                    selected: false,
                    disabled: false,
                }];

                let pakanElm = document.querySelector('#createModal .pakan');
                new Choices(pakanElm, {
                    choices: pakanOpt,
                    removeItemButton: true,
                    placeholder: true
                })
            });

            $("#createModal2").on("shown.bs.modal", function(e) {
                createModalId = $(this).attr('id');

                let pakanOpt = [{
                    value: 'FERMENTASI',
                    label: 'FERMENTASI',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'KONSETRAT',
                    label: 'KONSETRAT',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'COMPLETE FEED',
                    label: 'COMPLETE FEED',
                    selected: false,
                    disabled: false,
                }, {
                    value: 'RUMPUT',
                    label: 'RUMPUT',
                    selected: false,
                    disabled: false,
                }];

                let pakanElm = document.querySelector('#createModal2 .pakan');
                new Choices(pakanElm, {
                    choices: pakanOpt,
                    removeItemButton: true,
                    placeholder: true
                })
            });

            // Wilayah
            populateRegency()

            $('#province_id').on('change', function() {
                populateRegency()
            });

            function populateRegency() {
                let provinceId = $('#province_id').val()
                console.log(provinceId)
                if (provinceId == "" || provinceId == "null" || provinceId == null)
                    return

                $.ajax({
                    type: "GET",
                    url: "{{ route('reference.regency') }}",
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'province_id': provinceId
                    },
                    success: function(data) {
                        if (data.success == true) {
                            let options = "<option value='' disabled selected>Pilih kabupaten / kota</option>"

                            if (data.payload.lenth == 0) {
                                options += "<option value='' disabled selected>Tidak ada data kabupaten / kota</option>";
                            } else {
                                data.payload.forEach(val => {
                                    options += "<option value='" + val.id + "'> " + val.name + "</option>";
                                })
                            }

                            $('#regency_id').html(options)
                        } else {
                            alert('Terjadi kesalahan data')
                        }
                    },
                    error: function(error) {
                        alert('Terjadi kesalahan')
                    }
                });
            }

            $('#regency_id').on('change', function() {
                populateDistrict()
            });

            function populateDistrict() {
                let regencyId = $('#regency_id').val()
                console.log(regencyId)
                if (regencyId == "" || regencyId == "null" || regencyId == null)
                    return

                $.ajax({
                    type: "GET",
                    url: "{{ route('reference.district') }}",
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'regency_id': regencyId
                    },
                    success: function(data) {
                        if (data.success == true) {
                            let options = "<option value='' disabled selected>Pilih kecamatan</option>"

                            if (data.payload.lenth == 0) {
                                options += "<option value='' disabled selected>Tidak ada data kecamatan</option>";
                            } else {
                                data.payload.forEach(val => {
                                    options += "<option value='" + val.id + "'> " + val.name + "</option>";
                                })
                            }

                            $('#district_id').html(options)
                        } else {
                            alert('Terjadi kesalahan data')
                        }
                    },
                    error: function(error) {
                        alert('Terjadi kesalahan')
                    }
                });
            }

            $('#district_id').on('change', function() {
                populateVillage()
            });

            function populateVillage() {
                let districtId = $('#district_id').val()
                console.log(districtId)
                if (districtId == "" || districtId == "null" || districtId == null)
                    return

                $.ajax({
                    type: "GET",
                    url: "{{ route('reference.village') }}",
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'district_id': districtId
                    },
                    success: function(data) {
                        if (data.success == true) {
                            let options = "<option value='' disabled selected>Pilih desa</option>"

                            if (data.payload.lenth == 0) {
                                options += "<option value='' disabled selected>Tidak ada data desa</option>";
                            } else {
                                data.payload.forEach(val => {
                                    options += "<option value='" + val.id + "'> " + val.name + "</option>";
                                })
                            }

                            $('#village_id').html(options)
                        } else {
                            alert('Terjadi kesalahan data')
                        }
                    },
                    error: function(error) {
                        alert('Terjadi kesalahan')
                    }
                });
            }

            function storeData() {
                if (createModalId == '' || createModalId == 'undefined') {
                    return;
                }

                var formData = new FormData();
                let idModal = '#' + createModalId + ' '

                if (createModalId == 'createModal') {
                    formData.append('with_kandang', '0');
                    formData.append('kandang_id', $(idModal + "#kandang_id").val());
                } else if (createModalId == 'createModal2') {
                    formData.append('with_kandang', '1');
                    formData.append('farmer_id', $(idModal + "#farmer_id").val());
                    formData.append('kandang_type_id', $(idModal + "#kandang_type_id").val());
                    formData.append('name', $(idModal + "#name").val());
                    formData.append('panjang', $(idModal + "#panjang").val());
                    formData.append('lebar', $(idModal + "#lebar").val());
                    formData.append('luas', $(idModal + "#luas").val());
                    formData.append('jenis', $(idModal + "#jenis").val());
                    formData.append('province_id', $(idModal + "#province_id").val());
                    formData.append('regency_id', $(idModal + "#regency_id").val());
                    formData.append('district_id', $(idModal + "#district_id").val());
                    formData.append('village_id', $(idModal + "#village_id").val());
                    formData.append('address', $(idModal + "#address").val());
                    formData.append('rt_rw', $(idModal + "#rt_rw").val());
                    formData.append('longitude', $(idModal + "#longitude").val());
                    formData.append('latitude', $(idModal + "#latitude").val());
                }

                formData.append('nominal', $(idModal + "#nominal").val());
                formData.append('pakan', $(idModal + "#pakan").val());
                formData.append('limbah_id', $(idModal + "#limbah_id").val());
                formData.append('age', $(idModal + "#age").val());
                formData.append('type_id', $(idModal + "#livestock_type_id").val());
                formData.append('gender', $(idModal + "#gender").val());
                formData.append('acquired_month', $(idModal + "#month").val());
                formData.append('acquired_year', $(idModal + "#year").val());

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('livestock.store') }}",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data) {
                        gOverlay.hide();

                        if (data.success) {
                            toastr.success(data.message);
                            $("#createModal").modal("hide");
                            $("#createModal2").modal("hide");
                            $(".data-table").DataTable().ajax.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(error) {
                        gOverlay.hide();
                        toastr.error("Network/server error\r\n" + error);
                    }
                });
            }

            function editData(rowData) {
                $("#editModal").on("shown.bs.modal", function(e) {
                    $("#id_edit").val(rowData.id);
                    $("#code_edit").val(rowData.code);
                    $("#pakan_holder_edit").val(rowData.pakan);

                    let pakanOpt = [{
                        value: 'FERMENTASI',
                        label: 'FERMENTASI',
                        selected: false,
                        disabled: false,
                    }, {
                        value: 'KONSETRAT',
                        label: 'KONSETRAT',
                        selected: false,
                        disabled: false,
                    }, {
                        value: 'COMPLETE FEED',
                        label: 'COMPLETE FEED',
                        selected: false,
                        disabled: false,
                    }];
                    let ternakBesarKecilArr = ['SAPI', 'KERBAU', 'DOMBA', 'KAMBING'];
                    if (ternakBesarKecilArr.includes(rowData.livestock_type_kandang)) {
                        pakanOpt.push({
                            value: 'RUMPUT',
                            label: 'RUMPUT',
                            selected: false,
                            disabled: false,
                        });
                    }

                    console.log(pakanOpt);

                    let pakanElm = document.querySelector('.pakan_edit');
                    new Choices(pakanElm, {
                        choices: pakanOpt,
                        removeItemButton: true,
                        placeholder: true
                    })

                    $("#limbah_edit").val(rowData.limbah_id);
                    $("#age_edit").val(rowData.age);

                    let gender = rowData.gender;
                    if (gender == 'PEJANTAN')
                        gender = 'JANTAN';
                    else if (gender == 'INDUK')
                        gender = 'BETINA';

                    $("#gender_edit").val(gender);
                    $("#month_edit").val(rowData.month);
                    $("#year_edit").val(rowData.year);
                });
                $("#editModal").modal("show");
            }

            function updateData() {
                var formData = new FormData();
                formData.append('id', $("#id_edit").val());
                formData.append('pakan', $("#pakan_edit").val());
                formData.append('limbah', $("#limbah_edit").val());
                formData.append('age', $("#age_edit").val());
                formData.append('gender', $("#gender_edit").val());
                formData.append('month', $("#month_edit").val());
                formData.append('year', $("#year_edit").val());

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('livestock.update') }}",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data) {
                        gOverlay.hide();

                        if (data.success) {
                            toastr.success(data.message);
                            $("#editModal").modal("hide");
                            $(".data-table").DataTable().ajax.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(error) {
                        gOverlay.hide();
                        toastr.error("Network/server error\r\n" + error);
                    }
                });
            }

            function deleteData(id) {
                var formData = new FormData();
                formData.append('id', id);

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('livestock.delete') }}",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data) {
                        gOverlay.hide();

                        if (data.success) {
                            toastr.success(data.message);
                            $(".data-table").DataTable().ajax.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(error) {
                        gOverlay.hide();
                        toastr.error("Network/server error\r\n" + error);
                    }
                });
            }

            $(function() {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('livestock.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'farmer',
                            name: 'kandang.farmer.fullname'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'kandang',
                            name: 'kandang.name'
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
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });


            function saveData(id) {
                var formData = new FormData();
                formData.append('id', id);
                formData.append('status', $("#status" + id).val());

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('livestock.update-status') }}",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data) {
                        console.log(data);
                        gOverlay.hide();

                        if (data.success) {
                            toastr.success(data.message);
                            $(".data-table").DataTable().ajax.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(error) {
                        gOverlay.hide();
                        toastr.error("Network/server error\r\n" + error);
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
