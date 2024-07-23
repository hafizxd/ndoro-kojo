<table>
    <thead>
        <tr>
            <th scope="col" rowspan="3">No</th>
            <th style="text-align: center;" scope="col" rowspan="3">Jenis Ternak</th>
            <th style="text-align: center;" scope="col" rowspan="3">Jumlah Ternak</th>
            <th style="text-align: center;" scope="col" colspan="12">Mutasi Ternak</th>
            <th style="text-align: center;" scope="col" rowspan="3">Jumlah Ternak Terakhir</th>
        </tr>
        <tr>
            <th style="text-align: center;" scope="col" colspan="4">Jantan</th>
            <th style="text-align: center;" scope="col" colspan="4">Betina</th>
            <th style="text-align: center;" scope="col" colspan="4">All</th>
        </tr>
        <tr>
            <th style="text-align: center;" scope="col">Lahir</th>
            <th style="text-align: center;" scope="col">Mati</th>
            <th style="text-align: center;" scope="col">Jual</th>
            <th style="text-align: center;" scope="col">Beli</th>
            <th style="text-align: center;" scope="col">Lahir</th>
            <th style="text-align: center;" scope="col">Mati</th>
            <th style="text-align: center;" scope="col">Jual</th>
            <th style="text-align: center;" scope="col">Beli</th>
            <th style="text-align: center;" scope="col">Lahir</th>
            <th style="text-align: center;" scope="col">Mati</th>
            <th style="text-align: center;" scope="col">Jual</th>
            <th style="text-align: center;" scope="col">Beli</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = [];
            for ($i = 0; $i < 14; $i++) {
                $total[$i] = 0;
            }
        @endphp
        @foreach ($livestockStart as $value)
            @php 
                $livestock = $livestocks->firstWhere('id', $value->id);
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ (isset($value->livestockParent) ? $value->livestockParent->livestock_type . ' - ' : '') . $value->livestock_type }}</td>
                <td style="text-align: center;">{{ $value->total_ternak }} 
                    @php $total[0] += $value->total_ternak @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->lahir_jantan }}
                    @php $total[1] += $livestock->lahir_jantan @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->mati_jantan }}
                    @php $total[2] += $livestock->mati_jantan @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->jual_jantan }}
                    @php $total[3] += $livestock->jual_jantan @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->beli_jantan }}
                    @php $total[4] += $livestock->beli_jantan @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->lahir_betina }}
                    @php $total[5] += $livestock->lahir_betina @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->mati_betina }}
                    @php $total[6] += $livestock->mati_betina @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->jual_betina }}
                    @php $total[7] += $livestock->jual_betina @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->beli_betina }}
                    @php $total[8] += $livestock->beli_betina @endphp
                </td>
                <td style="text-align: center;">{{ $livestock->lahir_jantan + $livestock->lahir_betina }} 
                    @php 
                        $value->total_ternak += $livestock->lahir_jantan + $livestock->lahir_betina; 
                        $total[9] += $livestock->lahir_jantan + $livestock->lahir_betina;
                    @endphp</td>
                <td style="text-align: center;">{{ $livestock->mati_jantan + $livestock->mati_betina }} 
                    @php 
                        $value->total_ternak -= $livestock->mati_jantan + $livestock->mati_betina; 
                        $total[10] += $livestock->mati_jantan + $livestock->mati_betina;
                    @endphp</td>
                <td style="text-align: center;">{{ $livestock->jual_jantan + $livestock->jual_betina }} 
                    @php 
                        $value->total_ternak -= $livestock->jual_jantan + $livestock->jual_betina; 
                        $total[11] += $livestock->jual_jantan + $livestock->jual_betina; 
                    @endphp</td>
                <td style="text-align: center;">{{ $livestock->beli_jantan + $livestock->beli_betina }} 
                    @php 
                        $value->total_ternak += $livestock->beli_jantan + $livestock->beli_betina; 
                        $total[12] += $livestock->beli_jantan + $livestock->beli_betina;
                    @endphp</td>
                <td style="text-align: center;">{{ $value->total_ternak }}
                    @php $total[13] += $value->total_ternak @endphp
                </td>
            </tr> 
        @endforeach
        
        <tr>
            <td style="text-align: center;" colspan="2">Total</td>
            <td style="text-align: center;">{{ $total[0] }}</td>
            <td style="text-align: center;">{{ $total[1] }}</td>
            <td style="text-align: center;">{{ $total[2] }}</td>
            <td style="text-align: center;">{{ $total[3] }}</td>
            <td style="text-align: center;">{{ $total[4] }}</td>
            <td style="text-align: center;">{{ $total[5] }}</td>
            <td style="text-align: center;">{{ $total[6] }}</td>
            <td style="text-align: center;">{{ $total[7] }}</td>
            <td style="text-align: center;">{{ $total[8] }}</td>
            <td style="text-align: center;">{{ $total[9] }}</td>
            <td style="text-align: center;">{{ $total[10] }}</td>
            <td style="text-align: center;">{{ $total[11] }}</td>
            <td style="text-align: center;">{{ $total[12] }}</td>
            <td style="text-align: center;">{{ $total[13] }}</td>
        </tr>
    </tbody>
</table>
