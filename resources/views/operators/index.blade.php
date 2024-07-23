<x-app-layout>
    @section('title', 'Manajemen User | Operator')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack ms-n1 me-2"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="text-primary position-relative fw-bold mb-0"><span class="bg-soft pe-2">Manajemen User | Operator</span><span class="border-primary-200 position-absolute top-50 translate-middle-y w-100 z-index--1 start-0 border"></span></h3>
                </div>
            </div>

            <div class="card border-300 my-5 border shadow-none" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        @auth('web')
                            <div class="d-flex mb-5">
                                <div class="justify-content-end">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal">Tambah User</button>
                                </div>
                            </div>
                        @endauth

                        <table class="data-table table">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="10%">Nama</th>
                                    <th scope="col" width="10%">Username</th>
                                    <th scope="col" width="10%">Provinsi</th>
                                    <th scope="col" width="10%">Kabupaten / Kota</th>
                                    <th scope="col" width="10%">Kecamatan</th>
                                    <th scope="col" width="10%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @auth('web')
            <div class="modal modal-lg fade" id="createModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                        </div>
                        <div class="modal-body">
                            <form action="#" method="POST">
                                <div class="row flex-center">
                                    <div class="col-sm-12">
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Nama</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="name" type="text" placeholder="Nama" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Username</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="username" type="text" placeholder="Username" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Password</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="password" type="password" placeholder="Password" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Konfirmasi Password</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="confirm_password" type="password" placeholder="Password" />
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

                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" onClick="saveData()">Save</button>
                            <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="modal modal-lg fade" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                        </div>
                        <div class="modal-body">
                            <form action="#" method="POST">
                                <input type="hidden" id="id_edit">

                                <div class="row flex-center">
                                    <div class="col-sm-12">
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Nama</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="fullname_edit" type="text" placeholder="Nama" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Username</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="username_edit" type="text" placeholder="Username" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Email</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="email_edit" type="email" placeholder="Email" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Password</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="password_edit" type="password" placeholder="Password" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Phone</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="phone_edit" type="text" placeholder="Phone" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Address</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="address_edit" type="text" placeholder="Address" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Occupation</label>
                                            <div class="form-icon-container">
                                                <select id="occupation_edit" class="form-control">
                                                    <option value="PETERNAK">PETERNAK</option>
                                                    <option value="PEDAGANG TERNAK">PEDAGANG TERNAK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Gender</label>
                                            <div class="form-icon-container">
                                                <select id="gender_edit" class="form-control">
                                                    <option value="LAKI-LAKI">LAKI-LAKI</option>
                                                    <option value="PEREMPUAN">PEREMPUAN</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Age</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="age_edit" type="number" placeholder="Age" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Kelompok Ternak</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="kelompok_ternak_edit" type="text" placeholder="Kelompok Ternak" />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Provinsi</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="province_name_edit" type="text" placeholder="Provinsi" disabled />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Kabupaten / Kota</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="regency_name_edit" type="text" placeholder="Kabupaten / Kota" disabled />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Kecamatan</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="district_name_edit" type="text" placeholder="Kecamatan" disabled />
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label" for="email">Kelurahan / Desa</label>
                                            <div class="form-icon-container">
                                                <input class="form-control" id="village_name_edit" type="text" placeholder="Kelurahan / Desa" disabled />
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
            </div> --}}
        @endauth
    @endsection

    @push('script')
        <script src="{{ asset('assets/vendors/tinymce/tinymce.min.js') }}"></script>

        <script>
            $(function() {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('operator.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'username',
                            name: 'username'
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
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });

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

            function saveData() {
                var formData = new FormData();
                formData.append('name', $("#name").val());
                formData.append('username', $("#username").val());
                formData.append('password', $("#password").val());
                formData.append('confirm_password', $("#confirm_password").val());
                formData.append('district_id', $("#district_id").val());

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('operator.store') }}",
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

            function editData(rowData) {
                $("#editModal").on("shown.bs.modal", function(e) {
                    $("#id_edit").val(rowData.id);
                    $("#fullname_edit").val(rowData.fullname);
                    $("#username_edit").val(rowData.username);
                    $("#email_edit").val(rowData.email);
                    $("#password_edit").val(rowData.password);
                    $("#phone_edit").val(rowData.phone);
                    $("#address_edit").val(rowData.address);
                    $("#occupation_edit").val(rowData.occupation);
                    $("#gender_edit").val(rowData.gender);
                    $("#age_edit").val(rowData.age);
                    $("#kelompok_ternak_edit").val(rowData.kelompok_ternak);
                    $("#province_name_edit").val(rowData.province_name);
                    $("#regency_name_edit").val(rowData.regency_name);
                    $("#district_name_edit").val(rowData.district_name);
                    $("#village_name_edit").val(rowData.village_name);
                });
                $("#editModal").modal("show");
            }

            function updateData() {
                var formData = new FormData();
                formData.append('id', $("#id_edit").val());
                formData.append('name', $("#name").val());
                formData.append('username', $("#username").val());
                formData.append('password', $("#password").val());
                formData.append('confirm_password', $("#confirm_password").val());
                formData.append('district_id', $("#district_id").val());

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('operator.update') }}",
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
                    url: "{{ route('operator.delete') }}",
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
        </script>
    @endpush
</x-app-layout>
