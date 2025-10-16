@if($maintenances->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Maintenance</th>
                <th style="width: 25%;">Asset</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 18%;">Teknisi/Vendor</th>
                <th style="width: 12%;">Tanggal Mulai</th>
                <th style="width: 15%;">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $index => $maintenance)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $maintenance->kode_maintenance }}</td>
                    <td>
                        <strong>{{ $maintenance->asset->kode_asset }}</strong> - {{ $maintenance->asset->barang->nama_barang }}
                    </td>
                    <td class="text-center">
                        {{ $maintenance->maintenance_type == 'preventive' ? 'Preventive' : 'Corrective' }}
                    </td>
                    <td>
                        @if($maintenance->teknisi)
                            {{ $maintenance->teknisi->name }}
                        @elseif($maintenance->vendor_name)
                            {{ $maintenance->vendor_name }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $maintenance->tanggal_mulai->format('d-m-Y') }}</td>
                    <td class="text-right">Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">TOTAL:</td>
                <td class="text-right">Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Summary Simple --}}
    <div class="summary">
        <table style="width: 40%; margin: 20px auto; border: none;">
            <tr>
                <td style="border: none; padding: 5px; text-align: left;"><strong>Total Maintenance:</strong></td>
                <td style="border: none; padding: 5px; text-align: right;"><strong>{{ $stats['total'] }} kali</strong></td>
            </tr>
            <tr>
                <td style="border: none; padding: 5px; text-align: left;"><strong>Total Biaya:</strong></td>
                <td style="border: none; padding: 5px; text-align: right;"><strong>Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
@else
    <div class="no-data">
        <p>Tidak ada data maintenance untuk periode yang dipilih.</p>
    </div>
@endif