<x-app-layout>
    @section('title', 'Manajemen Slider')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
            <div class="col">
                <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Manajemen Slider</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
            </div>
            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                        <div class="card-body p-0">
                            <div class="p-4">
        
                                        <table class="table data-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" width="1%">#</th>
                                                    <th scope="col" width="20%">Nama Kategori</th>
                                                    <th scope="col" width="5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <form action="{{ route('slider.category.store') }}" method="POST">
                                                    @csrf
                                                    <tr>
                                                        <td></td>
                                                        <td><input type="text" name="title" class="form-control" required></td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </form>

                                                @php $count = 1; @endphp
                                                @foreach ($categories as $value)
                                                    <form action="{{ route('slider.category.update', $value->id) }}" method="POST">
                                                        @csrf
                                                        <tr>
                                                            <td>{{ $count }}</td>
                                                            <td><input type="text" name="title" value="{{ $value->title }}" class="form-control" required></td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <button type="submit" class="btn btn-success btn-sm">Edit</button>
                                                                    <button type="button" onClick="document.querySelector('#formDelete{{ $value->id }}').submit()" class="btn btn-danger btn-sm">Hapus</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </form>

                                                    <form action="{{ route('slider.category.delete', $value->id) }}" id="formDelete{{ $value->id }}" method="POST">
                                                        @csrf
                                                    </form>
                                                    @php $count++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    @endsection

    @push('script')
    @endpush
</x-app-layout>
