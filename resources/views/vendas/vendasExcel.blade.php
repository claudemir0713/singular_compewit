<table >
    <thead>
        <tr>
            <th align="center">NF</th>
            <th align="center">TIPO</th>
            <th align="center">VENDEDOR</th>
            <th align="center">CLIENTE</th>
            <th align="center">EMISSÃO</th>
            <th align="center">QTD</th>
            <th align="center">FAT1</th>
            <th align="center">FAT2</th>
            <th align="center">DESCONTO</th>
            <th align="center">ACRESCIMO</th>
            <th align="center">FRETE</th>
            <th align="center">IPI</th>
            <th align="center">FAT.TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vendas as $item )
            <tr>
                <td align="">{{ $item->DOCUMENTO }}</td>
                <td align="">{{ $item->TIPO_RECEITA  }}</td>
                <td align="">{{ $item->VENDEDOR  }}</td>
                <td align="">{{ $item->CLIENTE  }}</td>
                <td align="center">{{ date('d/m/Y', strtotime($item->DATA_COMP)) }}</td>
                <td align="right">{{ $item->QUANTIDADE }}</td>
                <td align="right">{{ $item->FAT_BRUTO }}</td>
                <td align="right">{{ $item->FAT_BRUTO_2 }}</td>
                <td align="right">{{ $item->DESCONTO }}</td>
                <td align="right">{{ $item->ACRESCIMO }}</td>
                <td align="right">{{ $item->FRETE }}</td>
                <td align="right">{{ $item->IPI }}</td>
                <td align="right">{{ $item->FAT_LIQUIDO }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

