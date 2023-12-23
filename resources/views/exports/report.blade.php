<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Total Ternak</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countTotalTernak }}</b></td>
            <td><b>ekor</b></td>
        </tr>
        @foreach ($arrTotalTernak as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Total Kandang</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countTotalKandang }}</b></td>
            <td><b>kandang</b></td>
        </tr>
        @foreach ($arrTotalKandang as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Transaksi Jual Beli</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countBeli }}</b></td>
            <td><b>ekor</b></td>
        </tr>
        @foreach ($arrBeli as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Sedang Dijual</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countJual }}</b></td>
            <td><b>ekor</b></td>
        </tr>
        @foreach ($arrJual as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Lahir</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countLahir }}</b></td>
            <td><b>ekor</b></td>
        </tr>
        @foreach ($arrLahir as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;"><b>Mati</b></th>    
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>{{ $countMati }}</b></td>
            <td><b>ekor</b></td>
        </tr>
        @foreach ($arrMati as $value)
            <tr>
                <td>{{ ucwords(strtolower($value->livestock_type)) }}</td>
                <td>{{ $value->count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- <th></th>
<th colspan="2">Total Kandang</th>
<th></th>
<th colspan="2">Transaksi Jual Beli</th>
<th></th>
<th colspan="2">Sedang Dijual</th>
<th></th>
<th colspan="2">Lahir</th>
<th></th>
<th colspan="2">Mati</th>
<th></th> --}}