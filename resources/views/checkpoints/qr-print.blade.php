<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR — {{ $area->area_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            padding: 24px;
            color: #111827;
        }

        .no-print { text-align: right; margin-bottom: 16px; }
        .no-print button {
            background: #1a1a2e;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .page-header { text-align: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 22px; font-weight: 700; }
        .page-header p { font-size: 13px; color: #6b7280; margin-top: 4px; }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .qr-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            break-inside: avoid;
        }
        .qr-card img {
            width: 140px;
            height: 140px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fff;
        }
        .qr-card .name { font-size: 13px; font-weight: 700; margin-top: 10px; line-height: 1.3; }
        .qr-card .code {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .empty { text-align: center; color: #6b7280; padding: 60px 0; font-size: 14px; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .qr-grid { gap: 12px; }
            .qr-card { border-radius: 8px; }
            .page-header h1 { font-size: 18px; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
    </div>

    <div class="page-header">
        <h1>QR Code Checkpoint — {{ $area->area_name }}</h1>
        <p>Sentinel Guard Systems &bull; Dicetak: {{ now()->format('d M Y H:i') }} &bull; Total {{ $checkpoints->count() }} checkpoint</p>
    </div>

    @if($checkpoints->isNotEmpty())
    <div class="qr-grid">
        @foreach($checkpoints as $cp)
        <div class="qr-card">
            <img src="{{ route('checkpoints.qr', $cp) }}" alt="QR {{ $cp->qr_code }}">
            <div class="name">{{ $cp->checkpoint_name }}</div>
            <div class="code">{{ $cp->qr_code }}</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty">Tidak ada checkpoint aktif di area ini.</div>
    @endif

</body>
</html>
