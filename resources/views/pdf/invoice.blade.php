<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaction->transaction_code }}</title>

    <style>
        /* === MINI TAILWIND STYLE FOR PDF === */

        body {
            font-family: sans-serif;
            margin: 15px;
            font-size: 11px;
            color: #374151;
        }

        .card {
            background: #ffffff;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .header {
            background: #2563eb;
            color: white;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        td {
            padding: 6px;
            border: 1px solid #e5e7eb;
        }

        .label {
            background: #f3f4f6;
            width: 35%;
            font-weight: bold;
        }

        .text-green { color: #16a34a; font-weight: bold; }
        .text-yellow { color: #ca8a04; font-weight: bold; }
        .text-red { color: #dc2626; font-weight: bold; }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-title">Invoice Pembayaran</div>
        <div style="margin-top:4px;">Kode Transaksi: <strong>{{ $transaction->transaction_code }}</strong></div>
    </div>

    <div class="card">

        <!-- CUSTOMER INFO -->
        <div class="section-title">Informasi Pelanggan</div>

        <table>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>{{ $transaction->fullName }}</td>
            </tr>
            <tr>
                <td class="label">Role</td>
                <td>{{ $transaction->role->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kategori</td>
                <td>{{ $transaction->category->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Telepon</td>
                <td>{{ $transaction->phoneNumber }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>
                    {{ $transaction->address['street'] }},
                    {{ $transaction->address['village'] }},
                    {{ $transaction->address['district'] }},
                    {{ $transaction->address['regencie'] }},
                    {{ $transaction->address['province'] }}
                </td>
            </tr>
        </table>

        <!-- TRANSACTION DETAILS -->
        <div class="section-title" style="margin-top:18px;">Detail Transaksi</div>

        <table>
            <tr>
                <td class="label">Kode Transaksi</td>
                <td>{{ $transaction->transaction_code }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah</td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Bulan / Tahun</td>
                <td>{{ $transaction->month }} / {{ $transaction->year }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>
                    @if ($transaction->status === 'paid')
                        <span class="text-green">Lunas</span>
                    @elseif ($transaction->status === 'pending')
                        <span class="text-yellow">Menunggu Pembayaran</span>
                    @else
                        <span class="text-red">Dibatalkan</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Dibayar Pada</td>
                <td>{{ $transaction->paid_at }}</td>
            </tr>
            <tr>
                <td class="label">Jatuh Tempo</td>
                <td>{{ $transaction->due_date }}</td>
            </tr>
        </table>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        @if ($transaction->status === 'paid')
            Terima kasih, pembayaran Anda telah diterima.
        @elseif ($transaction->status === 'pending')
            Silakan lakukan pembayaran sebelum jatuh tempo.
        @else
            Transaksi ini telah dibatalkan.
        @endif
        <br><br>
        Dicetak otomatis oleh sistem.
    </div>

</body>
</html>
