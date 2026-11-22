@php
    $homeUrl = url('/');
    $homeLabel = 'Kembali ke Halaman Utama';

    try {
        $user = auth()->user();
        if ($user) {
            $user->loadMissing('role');
            $roleName = strtolower((string) ($user->role?->nama ?? ''));
            $homeUrl = match ($roleName) {
                'admin' => Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin'),
                'owner' => Route::has('owner.dashboard') ? route('owner.dashboard') : url('/owner'),
                'kasir' => Route::has('kasir.beranda') ? route('kasir.beranda') : url('/kasir'),
                'karyawan' => Route::has('karyawan.pengantaran.index') ? route('karyawan.pengantaran.index') : url('/karyawan/pengantaran'),
                default => url('/'),
            };
            $homeLabel = 'Kembali ke Beranda';
        } elseif (request()->is('admin*')) {
            $homeUrl = url('/admin');
            $homeLabel = 'Beranda Admin';
        } elseif (request()->is('owner*')) {
            $homeUrl = url('/owner');
            $homeLabel = 'Beranda Owner';
        } elseif (request()->is('kasir*')) {
            $homeUrl = url('/kasir');
            $homeLabel = 'Beranda Kasir';
        } elseif (request()->is('karyawan*')) {
            $homeUrl = url('/karyawan/pengantaran');
            $homeLabel = 'Beranda Karyawan';
        }
    } catch (\Throwable $e) {
        $homeUrl = url('/');
    }

    $errorCode = trim($__env->yieldContent('code', '404'));
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title') | PASKA JAYA</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .pj-blueprint-bg {
            min-height: 100vh;
            margin: 0;
            padding: 32px 16px;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #F4F6FB;
            background-image:
                linear-gradient(to right, rgba(30, 64, 175, 0.045) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(30, 64, 175, 0.045) 1px, transparent 1px);
            background-size: 28px 28px;
            color: #1E293B;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .pj-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }
        .pj-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1E3A8A, #2563EB);
            color: #FFFFFF;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.22);
            border: 2px solid #FFFFFF;
        }
        .pj-error-card {
            width: 100%;
            max-width: 540px;
            background: #FFFFFF;
            border-radius: 24px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 24px 60px -15px rgba(30, 58, 138, 0.18);
            overflow: hidden;
        }
        .pj-error-hero {
            background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #2563EB 100%);
            padding: 34px 28px;
            text-align: center;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
        }
        .pj-icon-box {
            width: 68px;
            height: 68px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pj-float 4s ease-in-out infinite;
        }
        .pj-error-code {
            font-size: 64px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
        }
        .pj-error-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #DBEAFE;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .pj-error-body {
            padding: 32px 32px 34px;
            text-align: center;
        }
        .pj-error-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
        }
        .pj-error-desc {
            margin: 10px auto 0;
            max-width: 420px;
            font-size: 14px;
            line-height: 1.65;
            color: #475569;
        }
        .pj-url-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            padding: 7px 14px;
            max-width: 100%;
            border-radius: 12px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            font-size: 12px;
            color: #475569;
            box-sizing: border-box;
        }
        .pj-url-pill code {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 12px;
            color: #334155;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pj-actions {
            margin-top: 26px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .pj-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 12px;
            background: #1D4ED8;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s ease;
            white-space: nowrap;
        }
        .pj-btn-primary:hover { background: #1E40AF; }
        .pj-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 12px;
            background: #FFFFFF;
            color: #1E293B;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #CBD5E1;
            cursor: pointer;
            transition: background 0.15s ease;
            white-space: nowrap;
        }
        .pj-btn-secondary:hover { background: #F8FAFC; }
        @keyframes pj-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body class="pj-blueprint-bg">

    {{-- Brand Header --}}
    <div class="pj-brand">
        <div class="pj-brand-mark">PJ</div>
        <div style="line-height: 1.25;">
            <div style="font-size: 14px; font-weight: 800; color: #1E3A8A;">PASKA JAYA</div>
            <div style="font-size: 11px; font-weight: 600; color: #64748B;">Toko Bahan Bangunan</div>
        </div>
    </div>

    {{-- Centered Error Card --}}
    <div class="pj-error-card">
        <div class="pj-error-hero">
            <div class="pj-icon-box">
                @if($errorCode === '403')
                    <svg width="34" height="34" style="color: #FDE68A;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                @elseif($errorCode === '500')
                    <svg width="34" height="34" style="color: #FDE68A;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18.5h16.68a1 1 0 00.87-1.5l-7.5-13a1 1 0 00-1.74 0z" />
                    </svg>
                @else
                    <svg width="34" height="34" style="color: #FDE68A;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                @endif
            </div>

            <div class="pj-error-code">@yield('code')</div>

            <span class="pj-error-badge">
                <span style="width: 7px; height: 7px; border-radius: 9999px; background: #E59D2C; display: inline-block;"></span>
                @yield('badge', 'Status Sistem')
            </span>
        </div>

        <div class="pj-error-body">
            <h1 class="pj-error-title">@yield('title')</h1>
            <p class="pj-error-desc">@yield('message')</p>

            <div class="pj-url-pill">
                <span style="font-weight: 700; color: #1E3A8A;">URL:</span>
                <code>{{ request()->getRequestUri() }}</code>
            </div>

            <div class="pj-actions">
                <a href="{{ $homeUrl }}" class="pj-btn-primary">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ $homeLabel }}</span>
                </a>

                <button type="button" onclick="window.history.back()" class="pj-btn-secondary">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </button>
            </div>
        </div>
    </div>

    <p style="margin-top: 22px; font-size: 12px; color: #64748B;">
        &copy; {{ date('Y') }} PASKA JAYA — Sistem Manajemen Toko Bahan Bangunan
    </p>
</body>
</html>