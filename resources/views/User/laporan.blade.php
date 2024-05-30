@extends('layouts.user')

@section('css')
<link rel="icon" type="image/png" href="{{ asset('images/logo-pema.png') }}">
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
<style>
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

    .dropdown-menu {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .dropdown-item.profil,
    .dropdown-item.home {
        color: white;
    }

    .dropdown-item.logout {
        color: red;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .dropdown-item.logout:hover {
        color: white;
    }
</style>
@endsection

@section('title', 'PEMA - Pengaduan Masyarakat')

@section('content')
{{-- Section Header --}}
<section class="header">
    @if (Auth::guard('masyarakat')->check() && Auth::guard('masyarakat')->user()->email_verified_at == null)
    <div class="row">
        <div class="col">
            <div class="notification">
                Konfirmasi email <span class="font-weight-bold">{{ Auth::guard('masyarakat')->user()->email }}</span> untuk melindungi akun Anda.
                <form action="{{ route('pema.sendVerification') }}" method="POST" style="display: inline-block">
                    @csrf
                    <button type="submit" class="btn btn-white">Verifikasi Sekarang</button>
                </form>
            </div>
            @if (session()->has('verification_sent'))
                <div class="alert alert-success" style="text-align: center">
                {{ session('verification_sent') }}
        </div>
        @endif
        </div>
    </div>
    @endif
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h4 class="semi-bold mb-0" style="color: black;">PEMA</h4>
                <p class="italic mt-0" style="color: black;">Pengaduan Masyarakat</p>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(Auth::guard('masyarakat')->check())
                <ul class="navbar-nav text-center ml-auto">
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link ml-3 dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: black;">
                                {{ Auth::guard('masyarakat')->user()->nama }}
                            </a>
                            <!-- Dropdown menu -->
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item profil" href="{{ route('pema.profil') }}">Profil</a>
                                <a class="dropdown-item home" href="{{ route('pema.index') }}">Home</a>
                                <a class="dropdown-item logout" href="#" data-toggle="modal" data-target="#logoutConfirmationModal">Logout</a>
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
                        <a href="{{ route('pema.formRegister') }}" class="btn btn-outline-purple">Daftar</a>
                    </li>
                </ul>
                @endif
            </div>
        </div>
    </nav>
</section>

{{-- Section Card --}}
<div class="container">
    <div class="row justify-content-between">
        <div class="col-lg-8 col-md-12 col-sm-12 col-12 col">
            <div class="content content-top shadow">
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
                @endif
                @if (Session::has('pengaduan'))
                <div class="alert alert-{{ Session::get('type') }}">{{ Session::get('pengaduan') }}</div>
                @endif
                <div class="card mb-3">Tulis Laporan Disini</div>
                <form action="{{ route('pema.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="text" value="{{ old('judul_laporan') }}" name="judul_laporan" placeholder="Masukkan Judul Laporan" class="form-control">
                    </div>
                    <div class="form-group">
                        <textarea name="isi_laporan" placeholder="Masukkan Isi Laporan" class="form-control" rows="4">{{ old('isi_laporan') }}</textarea>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ old('tgl_kejadian') }}" name="tgl_kejadian" placeholder="Pilih Tanggal Kejadian" class="form-control" onfocusin="(this.type='date')" onfocusout="(this.type='text')">
                    </div>
                    <div class="form-group">
                        <textarea name="lokasi_kejadian" rows="3" class="form-control" placeholder="Lokasi Kejadian">{{ old('lokasi_kejadian') }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <select name="kategori_kejadian" class="custom-select" id="inputGroupSelect01" required>
                                <option value="" selected>Pilih Kategori Kejadian</option>
                                <option value="agama">Agama</option>
                                <option value="hukum">Hukum</option>
                                <option value="lingkungan">Lingkungan</option>
                                <option value="sosial">Sosial</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-custom mt-2">Kirim</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 col-12 col">
            <div class="content content-bottom shadow">
                <div>
                    <img src="{{ asset(Auth::guard('masyarakat')->user()->photo ? 'storage/' . Auth::guard('masyarakat')->user()->photo : 'images/user_default.svg') }}" alt="user profile" class="photo">
                    <div class="self-align">
                        <h5><a style="color: #6a70fc" href="#">{{ Auth::guard('masyarakat')->user()->nama }}</a></h5>
                        <p class="text-dark">{{ Auth::guard('masyarakat')->user()->username }}</p>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <p class="italic mb-0">Terverifikasi</p>
                            <div class="text-center">
                                {{ $hitung[0] }}
                            </div>
                        </div>
                        <div class="col">
                            <p class="italic mb-0">Proses</p>
                            <div class="text-center">
                                {{ $hitung[1] }}
                            </div>
                        </div>
                        <div class="col">
                            <p class="italic mb-0">Selesai</p>
                            <div class="text-center">
                                {{ $hitung[2] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-lg-8">
            <a class="d-inline tab {{ $siapa != 'me' ? 'tab-active' : ''}} mr-4" href="{{ route('pema.laporan') }}">
                Semua
            </a>
            <a class="d-inline tab {{ $siapa == 'me' ? 'tab-active' : ''}}" href="{{ route('pema.laporan', 'me') }}">
                Laporan Saya
            </a>
            <hr>
        </div>
        @foreach ($pengaduan as $k => $v)
        <div class="col-lg-8">
            <div class="laporan-top">
                <img src="{{ asset($v->user->photo ? 'storage/' . $v->user->photo : 'images/user_default.svg') }}" alt="user profile" class="photo">
                <div class="d-flex justify-content-between">
                    <div>
                        <p>{{ $v->user->nama }}</p>
                        @if ($v->status == '0')
                        <p class="text-danger">Pending</p>
                        @elseif($v->status == 'proses')
                        <p class="text-warning">{{ ucwords($v->status) }}</p>
                        @else
                        <p class="text-success">{{ ucwords($v->status) }}</p>
                        @endif
                    </div>
                    <div>
                        <p>{{ $v->tgl_pengaduan->format('d M, h:i') }}</p>
                    </div>
                    @if ($v->user->nik == Auth::guard('masyarakat')->user()->nik)
                    <div class="dropdown" style="background-color: transparent; color: white;">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: transparent; color: rgb(0, 0, 0);">
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: transparent; color: white;">
                            <form action="{{ route('pema.deletePengaduan', $v->id_pengaduan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger" style="background-color: transparent; color: white;">Hapus</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="laporan-mid">
                <div class="judul-laporan">
                    {{ $v->judul_laporan }}
                </div>
                <p>{{ $v->isi_laporan }}</p>
            </div>
            <div class="laporan-bottom">
                @if ($v->foto != null)
                <img src="{{ Storage::url($v->foto) }}" alt="{{ 'Gambar '.$v->judul_laporan }}" class="gambar-lampiran">
                @endif
                @if ($v->tanggapan != null)
                <p class="mt-3 mb-1">{{ '*Tanggapan dari '. $v->tanggapan->petugas->nama_petugas }}</p>
                <p class="light">{{ $v->tanggapan->tanggapan }}</p>
                @endif
            </div>
            <hr>
        </div>
        @endforeach
        </div>
    </div>

{{-- Footer --}}
<div class="mt-5">
    <hr>
    <div class="text-center">
        <p class="italic text-secondary"></p>
    </div>
</div>

{{-- Konfirmasi logout --}}
<div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutConfirmationModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="{{ route('pema.logout') }}" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
@if (Session::has('pesan'))
<script>
    $('#loginModal').modal('show');
</script>
@endif
@endsection
