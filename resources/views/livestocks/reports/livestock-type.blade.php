<x-app-layout>
    @section('title', 'Report Ternak')

    @push ('style')
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

            <div class="card shadow-none border border-300 my-5" data-component-card="data-component-card">
                <div class="card-body p-0">
                    <div class="p-4">
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
                                @foreach($livestockTypes as $key => $value)
                                    <tr>
                                        <td>
                                            @if (isset($value->children) && (count($value->children) > 1))
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
</x-app-layout>
