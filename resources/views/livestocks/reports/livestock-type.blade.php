<x-app-layout>
    @section('title', 'Report Ternak')

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
            <div class="col">
                <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Report Ternak</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
            </div>
            </div>

            <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">Ternak</th>
                                    <th scope="col" width="10%">Total Ternak</th>
                                    <th scope="col" width="10%">Transaksi Jual Beli</th>
                                    <th scope="col" width="10%">Sedang Dijual</th>
                                    <th scope="col" width="10%">Lahir</th>
                                    <th scope="col" width="10%">Mati</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($livestockTypes as $value)
                                    <tr>
                                        <td>{{ $value->livestock_type }}</td>
                                        <td><a href="{{ route('livestock.report.detail', ['total-ternak', $value->id]) }}">{{ $value->total_ternak }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['transaksi-jual-beli', $value->id]) }}">{{ $value->transaksi_jual_beli }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['sedang-dijual', $value->id]) }}">{{ $value->sedang_dijual }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['lahir', $value->id]) }}">{{ $value->lahir }}</a></td>
                                        <td><a href="{{ route('livestock.report.detail', ['mati', $value->id]) }}">{{ $value->mati }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-app-layout>
