<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>@yield('page_title') - {{ __(basicControl()->site_title) }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

    <!-- CSS Front Template -->
    <link rel="preload" href="{{ asset('assets/admin/css/theme.min.css') }}" data-hs-appearance="default"
          as="style">
    <link rel="preload" href="{{ asset('assets/admin/css/theme-dark.min.css') }}" data-hs-appearance="dark"
          as="style">

    <style data-hs-appearance-onload-styles>
        * {
            transition: unset !important;
        }

        body {
            opacity: 0;
        }
    </style>

    <script>
        window.hs_config = {
            "autopath": "@@autopath",
            "deleteLine": "hs-builder:delete",
            "deleteLine:build": "hs-builder:build-delete",
            "deleteLine:dist": "hs-builder:dist-delete",
            "previewMode": false,
            "startPath": "/index.html",
            "vars": {
                "themeFont": "https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap",
                "version": "?v=1.0"
            },
            "layoutBuilder": {
                "extend": {
                    "switcherSupport": true
                },
                "header": {
                    "layoutMode": "default",
                    "containerMode": "container-fluid"
                },
                "sidebarLayout": "default"
            },
            "themeAppearance": {
                "layoutSkin": "default",
                "sidebarSkin": "default",
                "styles": {
                    "colors": {
                        "primary": "#377dff",
                        "transparent": "transparent",
                        "white": "#fff",
                        "dark": "132144",
                        "gray": {
                            "100": "#f9fafc",
                            "900": "#1e2022"
                        }
                    },
                    "font": "Inter"
                }
            },
            "languageDirection": {
                "lang": "en"
            },
            "skipFilesFromBundle": {
                "dist": ["assets/js/hs.theme-appearance.js", "assets/js/hs.theme-appearance-charts.js",
                    "assets/js/demo.js"
                ],
                "build": ["assets/css/theme.css",
                    "assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js",
                    "assets/js/demo.js", "assets/css/theme-dark.css", "assets/css/docs.css",
                    "assets/vendor/icon-set/style.css", "assets/js/hs.theme-appearance.js",
                    "assets/js/hs.theme-appearance-charts.js",
                    "node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js",
                    "assets/js/demo.js"
                ]
            },
            "minifyCSSFiles": ["assets/css/theme.css", "assets/css/theme-dark.css"],
            "copyDependencies": {
                "dist": {
                    "*assets/js/theme-custom.js": ""
                },
                "build": {
                    "*assets/js/theme-custom.js": "",
                    "node_modules/bootstrap-icons/font/*fonts/**": "assets/css"
                }
            },
            "buildFolder": "",
            "replacePathsToCDN": {},
            "directoryNames": {
                "src": "./src",
                "dist": "./dist",
                "build": "./build"
            },
            "fileNames": {
                "dist": {
                    "js": "theme.min.js",
                    "css": "theme.min.css"
                },
                "build": {
                    "css": "theme.min.css",
                    "js": "theme.min.js",
                    "vendorCSS": "vendor.min.css",
                    "vendorJS": "vendor.min.js"
                }
            },
            "fileTypes": "jpg|png|svg|mp4|webm|ogv|json"
        }
    </script>
</head>

<style>
    .image-box {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .image-box::after {
        content: '';
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        position: absolute;
        left: 0;
        top: 0;
    }

    img.login-image {
        width: 100%;
        height: 100%;
    }

    main#content {
        overflow: hidden;
    }

    .login_page_logo{
        position: absolute;
        top: 20px;
        left: 40px;
        z-index: 999;
    }
</style>

<body>

<script src="{{ asset('assets/admin/js/hs.theme-appearance.js') }}"></script>

<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main pt-0">
    <div class="container-fluid p-0">
        @yield('content')
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->

<!-- JS Global Compulsory  -->
<script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery-migrate.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/hs-toggle-password.js') }}"></script>
<script src="{{ asset('assets/admin/js/theme.min.js') }}"></script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>



@stack('script')

<script>
    "use strict";
    $(document).ready(function() {

        console.log(HSThemeAppearance.getOriginalAppearance())
        HSThemeAppearance.setAppearance(HSThemeAppearance.getOriginalAppearance())

        window.onload = function() {
            new HSTogglePassword('.js-toggle-password')
        }
    })
</script>

</body>

</html>
