<table>
    <thead>
        <tr>
            <th scope="col">Peternak</th>
            <th scope="col">Kode Ternak</th>
            <th scope="col">Kandang</th>
            <th scope="col">Pakan</th>
            <th scope="col">Limbah</th>
            <th scope="col">Umur</th>
            <th scope="col">Jenis Kelamin</th>
            <th scope="col">Status Ternak</th>
            <th scope="col">Bulan</th>
            <th scope="col">Tahun</th>
            <th scope="col">Provinsi</th>
            <th scope="col">Desa</th>
            <th scope="col">Kecamatan</th>
            <th scope="col">Kelurahan/Desa</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($livestocks as $value)
            <tr>
                <td>{{ $value->kandang?->farmer?->fullname }}</td>
                <td>{{ $value->code }}</td>
                <td>{{ $value->kandang?->name }}</td>
                <td>{{ $value->pakan?->jenis_pakan }}</td>
                <td>{{ $value->limbah?->pengolahan_limbah }}</td>
                <td>{{ $value->age }}</td>
                <td>{{ $value->gender }}</td>
                <td>{{ isset($value->dead_year) ? 'MATI' : $value->acquired_status }}</td>
                <td>
                    @php 
                        $res = isset($value->dead_year) ? $value->dead_month : $value->acquired_month;
                        if (isset($res))
                            $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                        else
                            $res = '';
                    @endphp
                    {{ $res }}
                </td>
                <td>{{ isset($value->dead_year) ? $value->dead_year : $value->acquired_year }}</td>
                <td>{{ $value->kandang?->province?->name }}</td>
                <td>{{ $value->kandang?->regency?->name }}</td>
                <td>{{ $value->kandang?->district?->name }}</td>
                <td>{{ $value->kandang?->village?->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>