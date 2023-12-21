<x-app-layout>
    @section('title', 'Dashboard')

    @section('content')
        <div class="row g-4">
            <div class="col-12 col-xxl-6">
                <div class="mb-8">
                    <h2 class="mb-2">Dashboard</h2>
                </div>
            </div>
        </div>

        <div class="mb-9" data-bs-spy="scroll" data-bs-target="#widgets-scrollspy">
            <div class="d-flex mb-5" id="scrollspyStats"><span class="fa-stack me-2 ms-n1"><i class="fas fa-circle fa-stack-2x text-primary"></i><i class="fa-inverse fa-stack-1x text-primary-soft fas fa-percentage"></i></span>
              <div class="col">
                <h3 class="mb-0 text-primary position-relative fw-bold"><span class="bg-soft pe-2">Jumlah Ternak &amp; Transaksi</span><span class="border border-primary-200 position-absolute top-50 translate-middle-y w-100 start-0 z-index--1"></span></h3>
                {{-- <p class="mb-0">You can easily show your stats content by using these cards.</p> --}}
              </div>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-md-3 col-xxl-4">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="">
                        <div>
                          <h5 class="mb-1">Total Ternak</h5>
                        </div>
                      </div>
                      <div class="d-flex justify-content-center px-4 py-4 gap-2">
                        <h1>{{ number_format($countTotalTernak, 0, ",", ".") }}</h1>
                        <span class="text-secondary">ekor</span>
                      </div>
                      <div class="mt-2">
                        @foreach ($arrTotalTernak as $value)
                            <div class="d-flex align-items-center mb-2">
                                <div class="bullet-item bg-primary me-2"></div>
                                <h6 class="text-900 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                            </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3 col-xxl-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="">
                          <div>
                            <h5 class="mb-1">Total Kandang</h5>
                          </div>
                        </div>
                        <div class="d-flex justify-content-center px-4 py-4 gap-2">
                          <h1>{{ number_format($countTotalKandang, 0, ",", ".") }}</h1>
                          <span class="text-secondary">kandang</span>
                        </div>
                        <div class="mt-2">
                          @foreach ($arrTotalKandang as $value)
                              <div class="d-flex align-items-center mb-2">
                                  <div class="bullet-item bg-primary me-2"></div>
                                  <h6 class="text-900 fw-semi-bold flex-1 mb-0">Kandang {{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                  <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                              </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>

                <div class="col-md-3 col-xxl-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="">
                          <div>
                            <h5 class="mb-1">Transaksi Jual Beli</h5>
                          </div>
                        </div>
                        <div class="d-flex justify-content-center px-4 py-4 gap-2">
                          <h1>{{ number_format($countBeli, 0, ",", ".") }}</h1>
                          <span class="text-secondary">ekor</span>
                        </div>
                        <div class="mt-2">
                          @foreach ($arrBeli as $value)
                              <div class="d-flex align-items-center mb-2">
                                  <div class="bullet-item bg-primary me-2"></div>
                                  <h6 class="text-900 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                  <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                              </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                </div>

                <div class="col-md-3 col-xxl-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="">
                          <div>
                            <h5 class="mb-1">Sedang Dijual</h5>
                          </div>
                        </div>
                        <div class="d-flex justify-content-center px-4 py-4 gap-2">
                          <h1>{{ number_format($countJual, 0, ",", ".") }}</h1>
                          <span class="text-secondary">ekor</span>
                        </div>
                        <div class="mt-2">
                          @foreach ($arrJual as $value)
                              <div class="d-flex align-items-center mb-2">
                                  <div class="bullet-item bg-primary me-2"></div>
                                  <h6 class="text-900 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                  <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                              </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                </div>

                <div class="col-md-3 col-xxl-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="">
                          <div>
                            <h5 class="mb-1">Lahir</h5>
                          </div>
                        </div>
                        <div class="d-flex justify-content-center px-4 py-4 gap-2">
                          <h1>{{ number_format($countLahir, 0, ",", ".") }}</h1>
                          <span class="text-secondary">ekor</span>
                        </div>
                        <div class="mt-2">
                          @foreach ($arrLahir as $value)
                              <div class="d-flex align-items-center mb-2">
                                  <div class="bullet-item bg-primary me-2"></div>
                                  <h6 class="text-900 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                  <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                              </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                </div>

                <div class="col-md-3 col-xxl-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="">
                          <div>
                            <h5 class="mb-1">Mati</h5>
                          </div>
                        </div>
                        <div class="d-flex justify-content-center px-4 py-4 gap-2">
                          <h1>{{ number_format($countMati, 0, ",", ".") }}</h1>
                          <span class="text-secondary">ekor</span>
                        </div>
                        <div class="mt-2">
                          @foreach ($arrMati as $value)
                              <div class="d-flex align-items-center mb-2">
                                  <div class="bullet-item bg-primary me-2"></div>
                                  <h6 class="text-900 fw-semi-bold flex-1 mb-0">{{ ucwords(strtolower($value->livestock_type)) }}</h6>
                                  <h6 class="text-900 fw-semi-bold mb-0">{{ $value->count }}</h6>
                              </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-app-layout>