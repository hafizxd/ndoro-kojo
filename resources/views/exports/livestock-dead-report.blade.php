<table>
    <thead>
        <tr>
            <th scope="col" rowspan="3">No</th>
            <th style="text-align: center;" scope="col" rowspan="3">Jenis Ternak</th>
            <th style="text-align: center;" scope="col" rowspan="3">Jumlah Kematian</th>
            <th style="text-align: center;" scope="col" colspan="9">Sebab Kematian</th>
        </tr>
        <tr>
            <th style="text-align: center;" scope="col" colspan="3">Jantan</th>
            <th style="text-align: center;" scope="col" colspan="3">Betina</th>
            <th style="text-align: center;" scope="col" colspan="3">All</th>
        </tr>
        <tr>
            <th style="text-align: center;" scope="col">Penyakit</th>
            <th style="text-align: center;" scope="col">Dipotong</th>
            <th style="text-align: center;" scope="col">Bencana</th>
            <th style="text-align: center;" scope="col">Penyakit</th>
            <th style="text-align: center;" scope="col">Dipotong</th>
            <th style="text-align: center;" scope="col">Bencana</th>
            <th style="text-align: center;" scope="col">Penyakit</th>
            <th style="text-align: center;" scope="col">Dipotong</th>
            <th style="text-align: center;" scope="col">Bencana</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = [];
            for ($i = 0; $i < 10; $i++) {
                $total[$i] = 0;
            }
        @endphp
        @foreach ($livestocks as $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ (isset($value->livestockParent) ? $value->livestockParent->livestock_type . ' - ' : '') . $value->livestock_type }}</td>
                <td style="text-align: center;">{{ $value->livestocks->count() }} 
                    @php $total[0] += $value->livestocks->count() @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'JANTAN' && $item->dead_reason_2 == "PENYAKIT")->count();
                        echo $count;
                        $total[1] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'JANTAN' && $item->dead_reason_2 == "DIPOTONG")->count();
                        echo $count;
                        $total[2] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'JANTAN' && $item->dead_reason_2 == "BENCANA")->count();
                        echo $count;
                        $total[3] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'BETINA' && $item->dead_reason_2 == "PENYAKIT")->count();
                        echo $count;
                        $total[4] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'BETINA' && $item->dead_reason_2 == "DIPOTONG")->count();
                        echo $count;
                        $total[5] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->gender == 'BETINA' && $item->dead_reason_2 == "BENCANA")->count();
                        echo $count;
                        $total[6] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->dead_reason_2 == "PENYAKIT")->count();
                        echo $count;
                        $total[7] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->dead_reason_2 == "DIPOTONG")->count();
                        echo $count;
                        $total[8] += $count; 
                    @endphp
                </td>
                <td style="text-align: center;"> 
                    @php 
                        $count = $value->livestocks->filter(fn($item) => $item->dead_reason_2 == "BENCANA")->count();
                        echo $count;
                        $total[9] += $count; 
                    @endphp
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
        </tr>
    </tbody>
</table>
