<x-app-layout>
    @section('title', 'Ternak')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Ternak</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
                </div>
            </div>

            <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div class="d-flex mb-5">
                            <div class="justify-content-end">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Ternak</button>
                            </div>
                        </div>

                        <div class="table-responsive scrollbar">
                            <table class="table data-table">
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
                                                @foreach($farmers as $farmer)
                                                    @foreach ($farmer->kandangs as $value)
                                                        <option value="{{ $value->id }}">{{ $farmer->fullname ?? $farmer->code }} - {{ $value->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="email">Tipe Ternak</label>
                                        <div class="form-icon-container">
                                            <select id="livestock_type_id" class="form-control">
                                                @foreach ($livestockTypes as $value)
                                                    <option value="{{ $value->id }}">@if($value->level == 2) {{ $value->livestockParent?->livestock_type . ' - ' }} @endif {{ $value->livestock_type }}</option>
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
                                                <option value="INDUK">INDUK</option>
                                                <option value="PEJANTAN">PEJANTAN</option>
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
                                                <option value="INDUK">INDUK</option>
                                                <option value="PEJANTAN">PEJANTAN</option>
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
            $("#createModal").on("shown.bs.modal", function(e) {
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

                let pakanElm = document.querySelector('.pakan');
                new Choices(pakanElm, {
                    choices: pakanOpt,
                    removeItemButton: true,
                    placeholder: true
                })
            });

            function storeData() {
                var formData = new FormData();
                formData.append('kandang_id', $("#kandang_id").val());
                formData.append('pakan', $("#pakan").val());
                formData.append('limbah_id', $("#limbah_id").val());
                formData.append('age', $("#age").val());
                formData.append('type_id', $("#livestock_type_id").val());
                formData.append('gender', $("#gender").val());
                formData.append('acquired_month', $("#month").val());
                formData.append('acquired_year', $("#year").val());

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

            // function saveData(rowData) {
            //     $("#createModal").on("shown.bs.modal", function(e) {
            //         let pakanOpt = [{
            //             value: 'FERMENTASI',
            //             label: 'FERMENTASI',
            //             selected: false,
            //             disabled: false,
            //         }, {
            //             value: 'KONSETRAT',
            //             label: 'KONSETRAT',
            //             selected: false,
            //             disabled: false,
            //         }, {
            //             value: 'COMPLETE FEED',
            //             label: 'COMPLETE FEED',
            //             selected: false,
            //             disabled: false,
            //         }, {
            //             value: 'RUMPUT',
            //             label: 'RUMPUT',
            //             selected: false,
            //             disabled: false,
            //         }];

            //         let pakanElm = document.querySelector('.pakan');
            //         new Choices(pakanElm, {
            //             choices: pakanOpt,
            //             removeItemButton: true,
            //             placeholder: true
            //         })
            //     });
            //     $("#createModal").modal("show");
            // }

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

                    let gender = rowData.gender == 'JANTAN' ? 'PEJANTAN' : 'INDUK';
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
