<x-app-layout>
    @section('title', 'Ternak')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
            <div class="col">
                <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">{{ ucwords(str_replace('-', ' ', $urlType)) }} {{ ucwords(strtolower($livestockType->livestock_type)) }}</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
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
            $(function () {
                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('livestock.report.detail', [$urlType, $livestockType->id]) }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'farmer', name: 'farmer'},
                        {data: 'code', name: 'code'},
                        {data: 'kandang', name: 'kandang'},
                        {data: 'pakan', name: 'pakan'},
                        {data: 'limbah', name: 'limbah'},
                        {data: 'age', name: 'age'},
                        {data: 'gender', name: 'gender'},
                        {data: 'status', name: 'status'},
                        {data: 'month', name: 'month'},
                        {data: 'year', name: 'year'},
                        {data: 'province', name: 'province'},
                        {data: 'regency', name: 'regency'},
                        {data: 'district', name: 'district'},
                        {data: 'village', name: 'village'}
                    ]
                });
            });
       </script>
    @endpush
</x-app-layout>