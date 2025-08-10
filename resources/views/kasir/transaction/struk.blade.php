<div id="receipt" class="receipt">
    <img src="{{ asset('static/images/logo-apotek-v2.png') }}" width="200" class="center" alt="">
    @if (config('app.address'))
        <p>{{ config('app.address') }}</p>
    @endif

    @if (config('app.fonnte.phone_number'))
        <p>No. Telp {{ config('app.fonnte.phone_number') }}</p>
    @endif

    @if (config('app.struk.show_cashier_name'))
        <p>Kasir: {{ $transaction->kasir->name }}</p>
    @endif

    <hr>
    <p>{{ \Carbon\Carbon::parse($transaction->transaction_date)->locale('id')->translatedFormat('l, d F Y H:i') }} WIB
    </p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Qty</th>
                <th>Item</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->transactionDetails as $transactionDetail)
                <tr>
                    <td class="qty">{{ $transactionDetail->quantity }}</td>
                    <td style="text-align: left;">{{ $transactionDetail->drug->name }}</td>
                    <td>Rp. {{ number_format($transactionDetail->drug->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    @if (config('app.tax.enabled'))
        <div class="total">
            <span>Pajak ({{ config('app.tax.name') }})</span>
            <span>{{ number_format(config('app.tax.value'), 0, ',', '.') }}</span>
        </div>
    @endif
    <div class="total">
        <span>Total</span>
        <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
    </div>
    @if ($transaction->point_usage > 0)
        <div class="uang">
            <span>Poin Digunakan</span>
            <span>{{ number_format($transaction->point_usage, 0, ',', '.') }}</span>
        </div>
    @endif
    <div class="uang">
        <span>Uang</span>
        <span>{{ number_format($transaction->cash, 0, ',', '.') }}</span>
    </div>
    <div class="kembalian">
        <span>Kembalian</span>
        <span>{{ number_format($transaction->change, 0, ',', '.') }}</span>
    </div>
    <hr>
    <p class="thanks">Terima Kasih</p>
    <p class="link">Akses struk digital di bawah ini</p>
    <div id="qrcode" class="center link"></div>
</div>
