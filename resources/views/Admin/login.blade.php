<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-pema.png') }}">

    <style>
            .header-video {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 30%;
        min-width: 100%;
        min-height: 50%;
        width: 50%;
        height: auto;
        z-index: -1;
        object-fit: contain; 
    }
        body {
            background: #6a70fc;
        }

        .btn-purple {
            background: #6a70fc;
            width: 100%;
            color: #fff;
        }

        .card-body{
            background: rgba(255, 255, 255, 0.5); /* White background with 50% opacity */
        }

    </style>

    <title>Halaman Masuk Petugas</title>
</head>

<body>
<section class="header">
    <video autoplay loop muted class="header-video">
        <source src="{{ asset('videos/indo.mp4') }}" type="video/mp4">
        Browser Anda tidak mendukung video.
    </video>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <h2 class="text-center text-white mb-0 mt-5">PEMA</h2>
                <P class="text-center text-white mb-5">Pengaduan Masyarakat</P>
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="text-center mb-5">FORM PETUGAS</h2>
                        <form action="{{ route('admin.login') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="username" placeholder="Username" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Password" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-purple">MASUK</button>
                        </form>
                    </div>
                </div>
                
                <a href="{{ route('pema.index') }}" class="btn btn-warning text-white mt-3" style="width: 100%">Kembali
                    ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>

</html>
