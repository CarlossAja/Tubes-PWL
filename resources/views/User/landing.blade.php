@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .container-about {
	position: relative;
	display: grid;
	grid-template-columns: 1fr 1fr 1fr 1fr;
	gap: 1em;
	width: 800px;
	height: 500px;
	transition: all 400ms;
}

.container-about:hover .box {
	filter: grayscale(100%) opacity(24%);
}

.box {
	position: relative;
	background: var(--img) center center;
	background-size: cover;
	transition: all 400ms;
	display: flex;
	justify-content: center;
	align-items: center;
}

.container-about .box:hover {
	filter: grayscale(0%) opacity(100%);
}

.container-about:has(.box-1:hover) {
	grid-template-columns: 3fr 1fr 1fr 1fr;
}

.container-about:has(.box-2:hover) {
	grid-template-columns: 1fr 3fr 1fr 1fr;
}

.container-about:has(.box-3:hover) {
	grid-template-columns: 1fr 1fr 3fr 1fr;
}

.container-about:has(.box-4:hover) {
	grid-template-columns: 1fr 1fr 1fr 3fr;
}

.box:nth-child(odd) {
	transform: translateY(-16px);
}

.box:nth-child(even) {
	transform: translateY(16px);
}

.box::after {
	content: attr(data-text);
	position: absolute;
	bottom: 20px;
	background: #051622;
	color: #fff;
	padding: 10px 10px 10px 14px;
	letter-spacing: 4px;
	text-transform: uppercase;
	transform: translateY(60px);
	opacity: 0;
	transition: all 400ms;
}

.box:hover::after {
	transform: translateY(0);
	opacity: 1;
	transition-delay: 400ms;
}

    .notification {
        padding: 14px;
        text-align: center;
        background: #f4b704;
        color: #fff;
        font-weight: 300;
    }

    .btn-white {
        background: #fff;
        color: #000;
        text-transform: uppercase;
        padding: 0px 25px 0px 25px;
        font-size: 14px;
    }

    .btn-facebook {
        background: #3b66c4;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-facebook:hover {
        background: #3b66c4;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-google {
        background: #cf4332;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .btn-google:hover {
        background: #cf4332;
        width: 100%;
        color: #fff;
        font-weight: 600;
    }

    .header {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }

    
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

    .header-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 130px 0;
        color: #fff;
        background: transparent; 
    }

    .header-content h1 {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .header-content p {
        font-size: 24px;
        margin-bottom: 40px;
    }

    .intro-section {
        padding: 60px 0;
        text-align: center;
    }

    .intro-section h2 {
        font-size: 36px;
        margin-bottom: 20px;
    }

    .intro-section p {
        font-size: 18px;
        color: #666;
        max-width: 800px;
        margin: 0 auto;
        
    }

    .btn-ajukan-aduan {
        background-color: #f4b704; 
        color: #fff; 
        padding: 10px 20px; 
        border: none; 
        border-radius: 5px; 
        font-size: 18px; 
        transition: background-color 0.3s ease; 
    }

    .btn-ajukan-aduan:hover {
        background-color: #ecc72b; 
    }

</style>
@endsection

@section('title', 'PEKAT - Pengaduan Masyarakat')

@section('content')
{{-- Section Header --}}
<section class="header">
    <video autoplay loop muted class="header-video">
        <source src="{{ asset('videos/indo.mp4') }}" type="video/mp4">
        Browser Anda tidak mendukung video.
    </video>
    @if (Auth::guard('masyarakat')->check() && Auth::guard('masyarakat')->user()->email_verified_at == null)
<div class="row">
    <div class="col">
        <div id="verifyEmailNotification" class="notification" style="display: {{ session()->has('verification_sent') ? 'block' : 'none' }}; background-color: #28a745;">
            Email verifikasi telah dikirim. Silakan cek email Anda! 
            <form action="{{ route('pekat.sendVerification') }}" method="POST" style="display: inline-block">
                @csrf
                <button type="submit" class="btn btn-white" style="margin-left: 10px;">Click here to resend email!</button>
            </form>
        </div>
        <div id="confirmationNotification" class="notification" style="display: {{ session()->has('verification_sent') ? 'none' : 'block' }}">
            Konfirmasi email <span class="font-weight-bold">{{ Auth::guard('masyarakat')->user()->email }}</span> untuk melindungi akun Anda.
            <form action="{{ route('pekat.sendVerification') }}" method="POST" style="display: inline-block">
                @csrf
                <button type="submit" class="btn btn-white">Verifikasi Sekarang</button>
            </form>
        </div>
    </div>
</div>
@endif


    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h4 class="semi-bold mb-0 text-white">PEMA</h4>
                <p class="italic mt-0 text-white">Pengaduan Masyarakat</p>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(Auth::guard('masyarakat')->check())
                <ul class="navbar-nav text-center ml-auto">
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link ml-3 text-white dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::guard('masyarakat')->user()->nama }}
                            </a>
    
                            <!-- Dropdown menu -->
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('profile') }}">Profil</a>
                                <a class="dropdown-item" href="{{ route('pekat.logout') }}">Logout</a>
                            </div>
                        </div>
                    </li>
                </ul>
                @else
                <ul class="navbar-nav text-center ml-auto">
                    <li class="nav-item">
                        <button class="btn text-white" type="button" data-toggle="modal" data-target="#loginModal">Masuk</button>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pekat.formRegister') }}" class="btn btn-outline-purple">Daftar</a>
                    </li>
                </ul>
                @endif
            </div>
        </div>
    </nav>
    
    
    <div class="header-content">
        <h1>Selamat Datang di PEMA</h1>
        <p>Platform Pengaduan Masyarakat untuk Layanan yang Lebih Baik</p>

        {{-- Add conditional to display button if user is logged in --}}
        @if(Auth::guard('masyarakat')->check())
        <a href="{{ route('pekat.laporan') }}" class="btn btn-ajukan-aduan mt-0">Ajukan aduan</a>
        @endif
    </div>
</section>

{{-- Introduction Section --}}
<section class="intro-section">
    <div class="container">
        <h2>Tentang PEKAT</h2>
        <p>PEMA adalah platform yang memungkinkan masyarakat untuk melaporkan keluhannya terkait suatu masalah dengan mudah dan cepat yang dimana kami akan membantu dengan memberikan suatu solusi terhadap permasalahan tersebut untuk membantu pengguna dalam meyelesaikan masalah tersebut. Kami berkomitmen untuk meningkatkan kualitas layanan melalui transparansi dan respons yang cepat.</p>
    </div>

<br><br><br>
<center>
<div class="container"><h2>KELOMPOK 4</h2></div>
    <br><br>
    <div class="container-about">
        <div class="box box-1" style="--img: url(../images/buzz-removebg-preview.png);" data-text="apa"></div>
        <div class="box box-2" style="--img: url(../images/Brian-removebg.png);" data-text="brian"></div>
        <div class="box box-3" style="--img: url(../images/Doge\ 1.png);" data-text="dog"></div>
        <div class="box box-4" style="--img: url(../images/Earth.png);" data-text="p"></div>
    </div>
</center>
</section>


{{-- Footer --}}
<div class="mt-5">
    <hr>
    <div class="text-center">
        <p class="italic text-secondary">&copy; 2024 PEMA. All rights reserved.</p>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3 class="mt-3">Masuk terlebih dahulu</h3>
                <p>Silahkan masuk menggunakan akun yang sudah didaftarkan.</p>
                <label>Gunakan Akun Media Sosial Anda</label>
                <div class="row">
                    <div class="col">
                        <a href="{{ route('pekat.auth', 'facebook') }}" class="btn btn-facebook mb-2"><i class="fa fa-facebook" style="font-size:14px"></i> FACEBOOK</a>
                    </div>
                    <div class="col">
                        <a href="{{ route('pekat.auth', 'google') }}" class="btn btn-google"><i class="fa fa-google" style="font-size:14px"></i> GOOGLE</a>
                    </div>
                </div>
                <div class="text-center">
                    <p class="my-2 text-secondary">Atau</p>
                </div>
                <form action="{{ route('pekat.login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username atau Email</label>
                        <input type="text" name="username" id="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <a href="/admin">Petugas? Login Disini!!</a>
                    </div>
                    <button type="submit" class="btn btn-purple text-white mt-3" style="width: 100%">MASUK</button>
                </form>
                @if (Session::has('pesan'))
                <div class="alert alert-danger mt-2">
                    {{ Session::get('pesan') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@if (session()->has('verification_sent'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Menyembunyikan notifikasi konfirmasi email dan menampilkan notifikasi verifikasi email terkirim
        @if (session()->has('verification_sent'))
            document.getElementById('verifyEmailNotification').style.display = 'block';
            document.getElementById('confirmationNotification').style.display = 'none';
        @endif
    });
</script>
@endif
@endsection
