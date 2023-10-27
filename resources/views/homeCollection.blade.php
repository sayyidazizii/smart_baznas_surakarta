<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Siabas</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('resources/assets/favicon.ico') }}" />
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&amp;display=swap" rel="stylesheet" />
        <link href="{{ asset('resources/css/styles.css') }}" rel="stylesheet" />
    </head>
    <body>
        <img class="bg-img" src="{{ asset('resources/img/foto_depan.jpg') }}" style=""/>
        <div class="masthead light" style="padding-right: 5rem;">
            <img src="{{asset('resources/img/logosmart/logobaznas01.png')}}" id="logo" style="width: 330px; height: 330px; position:absolute; ">
        </div>
        
        <div class="social-icons">
            <div class="d-flex flex-row flex-lg-column justify-content-center align-items-center h-100 mt-3 mt-lg-0" style="text-align:left !important; display:inline-block; position:relative;">
                <a class="btn btn-light m-3" style="text-align:left !important" href="//smartbaznassragen.ciptapro.com"><img src="{{asset('resources/img/logosmart/logo smart 5-07.png')}}" id="logo" style="width: 160px; height: 80px;">SMArT : Sistem Manajemen Administrasi Terpadu</a>
                <a class="btn btn-light m-3" style="text-align:left !important" href="//ciptapro.com/actions_baznas_sragen"><img src="{{asset('resources/img/logosmart/logo-actions.png')}}" id="logo" style="width: 80px; height: 80px;">Actions : Accounting Online System</a>
                <a class="btn btn-light m-3" style="text-align:left !important" href="//ciptapro.com/cst_teams_baznas"><img src="{{asset('resources/img/logosmart/logo-teams.png')}}" id="logo" style="width: 60px; height: 60px;">Teams : Talent Management Systems</a>
            </div>
        </div>

        <div style="position:absolute; bottom: 0; right:0;">
            <a>www.ciptasolutindo.id</a>
        </div>

        <div class="social-icons">
            <div class="d-flex flex-row flex-lg-column justify-content-center align-items-center h-100 mt-3 mt-lg-0">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('resources/js/scripts.js') }}"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
