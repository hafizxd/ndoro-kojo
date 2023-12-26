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
                <td>
                    @php
                        if (isset($value->dead_year))
                            $res = 'MATI';
                        else if (isset($value->sold_year))
                            $res = 'JUAL';
                        else 
                            $res = $value->acquired_status;
                    @endphp     
                    {{ $res }}
                </td>
                <td>
                    @php 
                        if (isset($value->dead_year))
                            $res = $value->dead_month;
                        else if (isset($value->sold_year))
                            $res = $value->sold_month;
                        else 
                            $res = $value->acquired_month;
                        
                        if (isset($res))
                            $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                        else
                            $res = '';
                    @endphp
                    {{ $res }}
                </td>
                <td>
                    @php 
                        if (isset($value->dead_year))
                            $res = $value->dead_year;
                        else if (isset($value->sold_year))
                            $res = $value->sold_year;
                        else 
                            $res = $value->acquired_year;
                    @endphp
                    {{ $res }}
                </td>
                <td>{{ $value->kandang?->province?->name }}</td>
                <td>{{ $value->kandang?->regency?->name }}</td>
                <td>{{ $value->kandang?->district?->name }}</td>
                <td>{{ $value->kandang?->village?->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>