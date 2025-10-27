<!doctype html>
<html lang="id">
<head>
    <!-- Meta dasar -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />

    <title>{{ $title ?? 'SIMUDAH — Sistem Informasi Manajemen Persampahan Mudah & Terarah' }}</title>

    <!-- Meta SEO -->
    <meta name="description"
          content="SIMUDAH adalah Sistem Informasi Manajemen Persampahan yang dirancang agar pengelolaan sampah menjadi lebih mudah, transparan, dan terarah. Membantu masyarakat dan petugas dalam mendukung lingkungan bersih dan berkelanjutan." />
    <meta name="author" content="SIMUDAH Team" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#16a34a" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZGY5TYKW91"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-ZGY5TYKW91');
    </script>
    <!-- Open Graph -->
    <meta property="og:locale" content="id_ID" />
    <meta property="og:site_name" content="SIMUDAH" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $title ?? 'SIMUDAH — Sistem Informasi Manajemen Persampahan Mudah & Terarah' }}" />
    <meta property="og:description"
          content="Sistem Informasi Manajemen Persampahan Mudah & Terarah — membantu pengelolaan sampah menjadi lebih efisien, transparan, dan ramah lingkungan." />
    <meta property="og:image" content="{{ asset('images/og-image.png') }}" />
    <meta property="og:image:secure_url" content="{{ asset('images/og-image.png') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="SIMUDAH — Sistem Informasi Manajemen Persampahan Mudah & Terarah" />
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@simudah_id" />
    <meta name="twitter:title" content="{{ $title ?? 'SIMUDAH — Sistem Informasi Manajemen Persampahan Mudah & Terarah' }}" />
    <meta name="twitter:description"
          content="Sistem Informasi Manajemen Persampahan Mudah & Terarah — platform digital untuk pengelolaan sampah yang lebih baik." />
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}" />

    <!-- Fallback -->
    <link rel="image_src" href="{{ asset('images/og-image.png') }}" />

    <!-- Schema.org JSON-LD -->
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "SIMUDAH",
        "url": "https://simudah.test",
        "description": "Sistem Informasi Manajemen Persampahan Mudah & Terarah — mendukung pengelolaan sampah berbasis digital yang efisien dan transparan.",
        "applicationCategory": "Environmental Management Software",
        "operatingSystem": "Web",
        "inLanguage": "id",
        "image": {
            "@type": "ImageObject",
            "url": "https://simudah.test/images/og-image.png",
            "width": 1200,
            "height": 630,
            "caption": "SIMUDAH — Sistem Informasi Manajemen Persampahan Mudah & Terarah"
        },
        "publisher": {
            "@type": "Organization",
            "name": "SIMUDAH",
            "url": "https://simudah.test",
            "logo": {
                "@type": "ImageObject",
                "url": "https://simudah.test/images/logo-simudah.png",
                "width": 512,
                "height": 512
            }
        }
    }
    </script>
    @endverbatim

    <!-- Token, Favicon, dan CSS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}" />
    <script src="{{ asset('build/assets/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    @stack('styles')

    <script>
        localStorage.getItem("_x_darkMode_on") === "true" &&
            document.documentElement.classList.add("dark");
    </script>

    <style>
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradientMove 5s ease infinite;
        }
    </style>
</head>
    <body x-data class="is-header-blur" x-bind="$store.global.documentBody">
        <!-- App preloader-->
        <div
        class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900"
        >
        <div class="app-preloader-inner relative inline-block size-48"></div>
        </div>

        @yield('pages')
        @stack('scripts')
        <div id="x-teleport-target"></div>
        <script>
        window.addEventListener("DOMContentLoaded", () => Alpine.start());
        </script>
    </body>
    </html>
