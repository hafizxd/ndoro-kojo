<x-app-layout>
    @section('title', 'Dashboard')

    @push('style')
        <style>
            .table tr,
            .table td {
                padding: .3rem 0 !important;
            }
        </style>
    @endpush

    @section('content')
        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
                <div class="col">
                    <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Dashboard Ternak &amp; Transaksi</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
                </div>
            </div>
            <form action="#" class="my-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label" for="timepicker2">Select Time Range</label>
                            <input name="daterange" class="form-control datetimepicker flatpickr-input" id="timepicker2" type="text" placeholder="d-m-y to d-m-y" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end gap-3">
                        <button class="btn btn-secondary" type="button" onClick="clearFilterDate()">Clear</button>
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
            </form>
            <div class="d-flex mb-5">
                <div class="justify-content-end">
                    <a href="{{ route('report.export') }}" target="_blank" class="btn btn-primary" type="button">Download Report</a>
                </div>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Total Kandang</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 py-4 gap-2">
                                <h1 class="text-success">{{ number_format($countTotalKandang, 0, ',', '.') }}</h1>
                                <span class="text-secondary">kandang</span>
                            </div>
                            <div class="mt-2">
                                @foreach ($arrTotalKandang as $value)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bullet-item bg-success me-2"></div>
                                        <h6 class="text-1100 fw-semi-bold flex-1 mb-0">Kandang {{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                        <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count }}</h6>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border border-primary">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Total Ternak</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 pt-4  gap-2">
                                <h1 class="text-primary">{{ number_format($countTotalTernak['ALL'], 0, ',', '.') }}</h1>
                                <span class="text-secondary">ekor</span>
                            </div>
                            <div>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%"></th>
                                            <th style="width: 61%"></th>
                                            <th style="width: 19%"></th>
                                            <th style="width: 19%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Jantan</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Betina</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="bullet-item bg-primary me-2"></div> --}}
                                            </td>
                                            <td>
                                                <h6 class="text-1100 fw-bold flex-1 mb-0">Total Ternak</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countTotalTernak['JANTAN'] }}</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countTotalTernak['BETINA'] }}</h6>
                                            </td>
                                        </tr>
                                        @foreach ($arrTotalTernak as $value)
                                            <tr>
                                                <td>
                                                    <div class="bullet-item bg-primary me-2"></div>
                                                </td>
                                                <td>
                                                    <h6 class="text-1100 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_jantan }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_betina }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Transaksi Jual Beli</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 pt-4 gap-2">
                                <h1 class="text-info">{{ number_format($countBeli['ALL'], 0, ',', '.') }}</h1>
                                <span class="text-secondary">ekor</span>
                            </div>
                            <div>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%"></th>
                                            <th style="width: 61%"></th>
                                            <th style="width: 19%"></th>
                                            <th style="width: 19%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Jantan</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Betina</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="bullet-item bg-primary me-2"></div> --}}
                                            </td>
                                            <td>
                                                <h6 class="text-1100 fw-bold flex-1 mb-0">Transaksi Jual Beli</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countBeli['JANTAN'] }}</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countBeli['BETINA'] }}</h6>
                                            </td>
                                        </tr>
                                        @foreach ($arrBeli as $value)
                                            <tr>
                                                <td>
                                                    <div class="bullet-item bg-info me-2"></div>
                                                </td>
                                                <td>
                                                    <h6 class="text-1100 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_jantan }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_betina }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Sedang Dijual</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 pt-4 gap-2">
                                <h1 class="text-warning">{{ number_format($countJual['ALL'], 0, ',', '.') }}</h1>
                                <span class="text-secondary">ekor</span>
                            </div>
                            <div>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%"></th>
                                            <th style="width: 61%"></th>
                                            <th style="width: 19%"></th>
                                            <th style="width: 19%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Jantan</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Betina</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="bullet-item bg-primary me-2"></div> --}}
                                            </td>
                                            <td>
                                                <h6 class="text-1100 fw-bold flex-1 mb-0">Sedang Dijual</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countJual['JANTAN'] }}</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countJual['BETINA'] }}</h6>
                                            </td>
                                        </tr>
                                        @foreach ($arrJual as $value)
                                            <tr>
                                                <td>
                                                    <div class="bullet-item bg-warning me-2"></div>
                                                </td>
                                                <td>
                                                    <h6 class="text-1100 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_jantan }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_betina }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border border-secondary">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Lahir</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 pt-4 gap-2">
                                <h1 class="text-secondary">{{ number_format($countLahir['ALL'], 0, ',', '.') }}</h1>
                                <span class="text-secondary">ekor</span>
                            </div>
                            <div>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%"></th>
                                            <th style="width: 61%"></th>
                                            <th style="width: 19%"></th>
                                            <th style="width: 19%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Jantan</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Betina</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="bullet-item bg-primary me-2"></div> --}}
                                            </td>
                                            <td>
                                                <h6 class="text-1100 fw-bold flex-1 mb-0">Lahir</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countLahir['JANTAN'] }}</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countLahir['BETINA'] }}</h6>
                                            </td>
                                        </tr>
                                        @foreach ($arrLahir as $value)
                                            <tr>
                                                <td>
                                                    <div class="bullet-item bg-secondary me-2"></div>
                                                </td>
                                                <td>
                                                    <h6 class="text-1100 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_jantan }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_betina }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border border-danger">
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <h5 class="mb-1">Mati</h5>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center px-4 pt-4 gap-2">
                                <h1 class="text-danger">{{ number_format($countMati['ALL'], 0, ',', '.') }}</h1>
                                <span class="text-secondary">ekor</span>
                            </div>
                            <div>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%"></th>
                                            <th style="width: 61%"></th>
                                            <th style="width: 19%"></th>
                                            <th style="width: 19%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Jantan</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">Betina</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="bullet-item bg-primary me-2"></div> --}}
                                            </td>
                                            <td>
                                                <h6 class="text-1100 fw-bold flex-1 mb-0">Mati</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countMati['JANTAN'] }}</h6>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="text-1100 fw-semi-bold mb-0">{{ $countMati['BETINA'] }}</h6>
                                            </td>
                                        </tr>
                                        @foreach ($arrMati as $value)
                                            <tr>
                                                <td>
                                                    <div class="bullet-item bg-danger me-2"></div>
                                                </td>
                                                <td>
                                                    <h6 class="text-1100 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_jantan }}</h6>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="text-1100 fw-semi-bold mb-0">{{ $value->count_betina }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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

            $(document).ready(function() {
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
