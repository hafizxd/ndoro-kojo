<x-app-layout>
    @section('title', 'Dashboard')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
            <div class="col">
                <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Brebes Today</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
            </div>
            </div>

            <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div class="d-flex mb-5">
                            <div class="justify-content-end">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Slider</button>
                            </div>
                        </div>

                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="10%">Thumbnail</th>
                                    <th scope="col" width="20%">Title</th>
                                    <th scope="col" width="30%">Content</th>
                                    <th scope="col" width="10%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-xl fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Tambah Slider</h5>
                  <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST">
                        <div class="row flex-center">
                          <div class="col-sm-12">
                            <div class="mb-3 text-start">
                              <label class="form-label" for="email">Title</label>
                              <div class="form-icon-container">
                                <input class="form-control" id="title" type="text" placeholder="Title" />
                              </div>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label" for="email">Thumbnail</label>
                                <div class="form-icon-container">
                                  <input class="form-control" id="thumbnail" type="file" accept="image/*" />
                                </div>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label" for="email">Content</label>
                                <div class="form-icon-container">
                                    <textarea id="content" class="tinymce" name="content" data-tinymce="{}"></textarea>
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

        <div class="modal modal-xl fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Edit Slider</h5>
                  <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST">
                        <div class="row flex-center">
                          <div class="col-sm-12">
                            <input type="hidden" id="idEdit">

                            <div class="mb-3 text-start">
                              <label class="form-label" for="email">Title</label>
                              <div class="form-icon-container">
                                <input class="form-control" id="titleEdit" type="text" placeholder="Title" />
                              </div>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label" for="email">Thumbnail</label>
                                <div class="form-icon-container">
                                  <input class="form-control" id="thumbnailEdit" type="file" accept="image/*" />
                                </div>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label" for="email">Content</label>
                                <div class="form-icon-container">
                                    <textarea id="contentEdit" class="tinymce" name="contentEdit" data-tinymce="{}"></textarea>
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
        <script src="{{asset('assets/vendors/tinymce/tinymce.min.js')}}"></script>
        
       <script>
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('slider.today') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'thumbnail', name: 'thumbnail'},
                        {data: 'title', name: 'title'},
                        {data: 'content', name: 'content'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
                });
            });

            function saveData() {
                var formData = new FormData();
                formData.append('type', 'TODAY');
                formData.append('title', $("#title").val());
                formData.append('thumbnail', $("#thumbnail")[0].files[0]);
                formData.append('content', tinymce.get("content").getContent());
                console.log(formData);

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('slider.store') }}",
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
                $("#editModal").on("shown.bs.modal", function (e) {
                    $("#idEdit").val(rowData.id);
                    $("#thumbnailEdit").val('');
                    $("#titleEdit").val(rowData.title);
                    tinymce.get("contentEdit").setContent(rowData.content.replaceAll('<br>', '\n'));
                });
                $("#editModal").modal("show");   
            }

            function updateData() {
                let thumbnail = $("#thumbnailEdit")[0].files[0];
                if (typeof thumbnail == 'undefined') 
                    thumbnail = '';

                var formData = new FormData();
                formData.append('type', 'TODAY');
                formData.append('id', $("#idEdit").val());
                formData.append('title', $("#titleEdit").val());
                formData.append('thumbnail', thumbnail);
                formData.append('content', tinymce.get("contentEdit").getContent());
                console.log(formData);

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('slider.update') }}",
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
                formData.append('type', 'TODAY');
                formData.append('id', id);

                createOverlay("Proses...");

                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    type: "POST",
                    url: "{{ route('slider.delete') }}",
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
