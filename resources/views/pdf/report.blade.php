<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        color: #333;
    }

    h2 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .periode {
        font-size: 14px;
        margin-bottom: 20px;
        color: #555;
    }

    .cards {
        width: 100%;
        margin-bottom: 20px;
        display: table;
        border-spacing: 15px;
    }

    .card {
        display: table-cell;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .card h4 {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .card p {
        font-size: 18px;
        font-weight: bold;
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table th,
    table td {
        border: 1px solid #ccc;
        padding: 8px 10px;
        font-size: 14px;
    }

    table th {
        background: #007bff;
        color: white;
        text-align: left;
    }

    table tr:nth-child(even) {
        background: #f2f2f2;
    }
</style>

<h2>Laporan Penjualan</h2>
<div class="periode">Periode: {{ $start }} s/d {{ $end }}</div>

<div class="cards">
    <div class="card">
        <h4>Total Omzet</h4>
        <p>Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
    </div>
    <div class="card">
        <h4>Total Keuntungan</h4>
        <p>Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</p>
    </div>
    <div class="card">
        <h4>Jumlah Transaksi</h4>
        <p>{{ $jumlahTransaksi }}</p>
    </div>
    <div class="card">
        <h4>Produk Terjual</h4>
        <p>{{ $produkTerjual }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $t)
            <tr>
                <td>{{ $t->transaction_date }}</td>
                <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                <td>{{ ucfirst($t->payment_method) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if (isset($chartBase64))
    <div style="margin-top: 30px;">
        <h4 style="margin-bottom: 10px;">Grafik Penjualan & Keuntungan</h4>
        <img src="{{ $chartBase64 }}" alt="Chart Penjualan & Keuntungan" style="width: 100%; max-height: 350px;">
    </div>
@endif
