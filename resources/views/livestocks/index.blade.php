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
                        {{-- <div class="d-flex mb-5">
                            <div class="justify-content-end">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal">Tambah User</button>
                            </div>
                        </div> --}}

                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="10%">Peternak</th>
                                    <th scope="col" width="10%">Kandang</th>
                                    <th scope="col" width="10%">Ras</th>
                                    <th scope="col" width="10%">Status Ternak</th>
                                    <th scope="col" width="10%">Kecamatan</th>
                                    <th scope="col" width="10%">Kelurahan/Desa</th>
                                    <th scope="col" width="10%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
       <script>
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('livestock.index') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'farmer', name: 'farmer'},
                        {data: 'kandang', name: 'kandang'},
                        {data: 'ras', name: 'ras'},
                        {data: 'acquired_status', name: 'acquired_status'},
                        {data: 'district', name: 'district'},
                        {data: 'village', name: 'village'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
                });
            });
       </script>

    @endpush
</x-app-layout>
