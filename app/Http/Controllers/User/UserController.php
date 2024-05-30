<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\VerifikasiEmailUntukRegistrasiPengaduanMasyarakat;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index()
    {
        // Menghitung jumlah pengaduan yang ada di table
        $pengaduan = Pengaduan::all()->count();

        // Arahkan ke file user/landing.blade.php
        return view('user.landing', ['pengaduan' => $pengaduan]);
    }

    public function login(Request $request)
    {
        // Pengecekan $request->username isinya email atau username
        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            // jika isinya string email, cek email nya di table masyarakat
            $email = Masyarakat::where('email', $request->username)->first();

            // Pengecekan variable $email jika tidak ada di table masyarakat
            if (!$email) {
                return redirect()->back()->with(['pesan' => 'Email tidak terdaftar']);
            }

            // jika email ada, langsung check password yang dikirim di form dan di table, hasilnya sama atau tidak
            $password = Hash::check($request->password, $email->password);

            // Pengecekan variable $password jika password tidak sama dengan yang dikirimkan
            if (!$password) {
                return redirect()->back()->with(['pesan' => 'Password tidak sesuai']);
            }

            // Jalankan fungsi auth jika berjasil melewati validasi di atas
            if (Auth::guard('masyarakat')->attempt(['email' => $request->username, 'password' => $request->password])) {
                // Jika login berhasil
                return redirect()->back();
            } else {
                // Jika login gagal
                return redirect()->back()->with(['pesan' => 'Akun tidak terdaftar!']);
            }
        } else {
            // jika isinya string username, cek username nya di table masyarakat
            $username = Masyarakat::where('username', $request->username)->first();

            // Pengecekan variable $username jika tidak ada di table masyarakat
            if (!$username) {
                return redirect()->back()->with(['pesan' => 'Username tidak terdaftar']);
            }

            // jika username ada, langsung check password yang dikirim di form dan di table, hasilnya sama atau tidak
            $password = Hash::check($request->password, $username->password);

            // Pengecekan variable $password jika password tidak sama dengan yang dikirimkan
            if (!$password) {
                return redirect()->back()->with(['pesan' => 'Password tidak sesuai']);
            }

            // Jalankan fungsi auth jika berjasil melewati validasi di atas
            if (Auth::guard('masyarakat')->attempt(['username' => $request->username, 'password' => $request->password])) {
                // Jika login berhasil
                return redirect()->back();
            } else {
                // Jika login gagal
                return redirect()->back()->with(['pesan' => 'Akun tidak terdaftar!']);
            }
        }
    }

    public function formRegister()
    {
        // Arahkan ke file user/register.blade.php
        return view('user.register');
    }

    public function register(Request $request)
    {
        // Masukkan semua data yg dikirim ke variable $data 
        $data = $request->all();

        // Buat variable $validate kemudian isinya Validator::make(datanya, [nama_field => peraturannya])
        $validate = Validator::make($data, [
            'nik' => ['required', 'unique:masyarakat'],
            'nama' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:masyarakat'],
            'username' => ['required', 'string', 'regex:/^\S*$/u', 'unique:masyarakat'],
            'password' => ['required', 'min:6'],
            'telp' => ['required'],
        ]);

        // Pengecekan jika validate fails atau gagal
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        // Mengecek email
        $email = Masyarakat::where('email', $request->username)->first();

        // Pengecekan jika email sudah terdaftar
        if ($email) {
            return redirect()->back()->with(['pesan' => 'Email sudah terdaftar'])->withInput(['email' => 'asd']);
        }

        // Mengecek username
        $username = Masyarakat::where('username', $request->username)->first();

        // Pengecekan jika username sudah terdaftar
        if ($username) {
            return redirect()->back()->with(['pesan' => 'Username sudah terdaftar'])->withInput(['username' => null]);
        }

        // Memasukkan data kedalam table Masyarakat
        Masyarakat::create([
            'nik' => $data['nik'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'telp' => $data['telp'],
        ]);

        // Kirim link verifikasi email
        // $link = URL::temporarySignedRoute('pema.verify', now()->addMinutes(30), ['nik' => $data['nik']]);
        // Mail::to($data['email'])->send(new VerifikasiEmailUntukRegistrasiPengaduanMasyarakat($data['nama'], $link));

        // Arahkan ke route pema.index
        return redirect()->route('pema.index');
    }

    public function logout()
    {
        // Fungsi logout dengan guard('masyarakat')
        Auth::guard('masyarakat')->logout();

        // Arahkan ke route pema.index
        return redirect()->route('pema.index');
    }

    public function storePengaduan(Request $request)
    {
        // Pengecekan jika tidak ada masyarakat yang sedang login
        if (!Auth::guard('masyarakat')->user()) {
            return redirect()->back()->with(['pesan' => 'Login dibutuhkan!'])->withInput();
        }

        // Masukkan semua data yg dikirim ke variable $data 
        $data = $request->all();

        // Buat variable $validate kemudian isinya Validator::make(datanya, [nama_field => peraturannya])
        $validate = Validator::make($data, [
            'judul_laporan' => ['required'],
            'isi_laporan' => ['required'],
            'tgl_kejadian' => ['required'],
            'lokasi_kejadian' => ['required'],
            'kategori_kejadian' => ['required'],
        ]);

        // Pengecekan jika validate fails atau gagal
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        // Pengecekan jika ada file foto yang dikirim
        if ($request->file('foto')) {
            $data['foto'] = $request->file('foto')->store('assets/pengaduan', 'public');
        }

        // Set timezone waktu ke Asia/Bangkok
        date_default_timezone_set('Asia/Bangkok');

        // Membuat variable $pengaduan isinya Memasukkan data kedalam table Pengaduan
        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => date('Y-m-d h:i:s'),
            'nik' => Auth::guard('masyarakat')->user()->nik,
            'judul_laporan' => $data['judul_laporan'],
            'isi_laporan' => $data['isi_laporan'],
            'tgl_kejadian' => $data['tgl_kejadian'],
            'lokasi_kejadian' => $data['lokasi_kejadian'],
            'kategori_kejadian' => $data['kategori_kejadian'],
            'foto' => $data['foto'] ?? '',
            'status' => '0',
        ]);

        // Pengecekan variable $pengaduan
        if ($pengaduan) {
            // Jika mengirim pengaduan berhasil
            return redirect()->route('pema.laporan', 'me')->with(['pengaduan' => 'Berhasil terkirim!', 'type' => 'success']);
        } else {
            // Jika mengirim pengaduan gagal
            return redirect()->back()->with(['pengaduan' => 'Gagal terkirim!', 'type' => 'danger']);
        }
    }

    public function laporan($siapa = '')
    {
        // Membuat variable $terverifikasi isinya menghitung pengaduan status pending
        $terverifikasi = Pengaduan::where([['nik', Auth::guard('masyarakat')->user()->nik], ['status', '!=', '0']])->get()->count();
        // Membuat variable $terverifikasi isinya menghitung pengaduan status proses
        $proses = Pengaduan::where([['nik', Auth::guard('masyarakat')->user()->nik], ['status', 'proses']])->get()->count();
        // Membuat variable $terverifikasi isinya menghitung pengaduan status selesai
        $selesai = Pengaduan::where([['nik', Auth::guard('masyarakat')->user()->nik], ['status', 'selesai']])->get()->count();

        // Masukkan 3 variable diatas ke dalam variable array $hitung
        $hitung = [$terverifikasi, $proses, $selesai];

        // Pengecekan jika ada parameter $siapa yang dikirimkan di url
        if ($siapa == 'me') {
            // Jika $siapa isinya 'me'
            $pengaduan = Pengaduan::where('nik', Auth::guard('masyarakat')->user()->nik)->orderBy('tgl_pengaduan', 'desc')->get();

            // Arahkan ke file user/laporan.blade.php sebari kirim data pengaduan, hitung, siapa
            return view('user.laporan', ['pengaduan' => $pengaduan, 'hitung' => $hitung, 'siapa' => $siapa]);
        } else {
            // Jika $siapa kosong
            $pengaduan = Pengaduan::where([['nik', '!=', Auth::guard('masyarakat')->user()->nik], ['status', '!=', '0']])->orderBy('tgl_pengaduan', 'desc')->get();

            // Arahkan ke file user/laporan.blade.php sebari kirim data pengaduan, hitung, siapa
            return view('user.laporan', ['pengaduan' => $pengaduan, 'hitung' => $hitung, 'siapa' => $siapa]);
        }
    }

    public function deletePengaduan($id)
{
    // Cari pengaduan berdasarkan ID
    $pengaduan = Pengaduan::find($id);

    // Pastikan pengaduan ditemukan
    if ($pengaduan) {
        // Pastikan pengguna yang menghapus laporan adalah pemilik laporan
        if ($pengaduan->user->id == Auth::guard('masyarakat')->user()->id) {
            // Mulai transaksi database
            DB::beginTransaction();

            try {
                // Hapus semua hubungan pengaduan dengan tanggapan
                $pengaduan->tanggapan()->delete();

                // Hapus laporan
                $pengaduan->delete();

                // Commit transaksi
                DB::commit();

                return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
            } catch (\Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                DB::rollback();
                return redirect()->back()->with('error', 'Gagal menghapus laporan. Silakan coba lagi.');
            }
        } else {
            // Jika pengguna bukan pemilik laporan
            return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus laporan ini.');
        }
    } else {
        // Jika laporan tidak ditemukan
        return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
    }
}
public function profil()
{
    // Ambil data pengguna yang sedang login
    $user = Auth::guard('masyarakat')->user();
    // Tampilkan halaman profil dengan data pengguna
    return view('profile', compact('user'));
}

public function updateProfile(Request $request)
{
    // Validasi data yang dikirimkan
    $validatedData = $request->validate([
        'nama' => 'required|string',
        'username' => ['required', 'string'],
        'telp' => 'required|string',
    ]);

    // Mulai transaksi database
    DB::beginTransaction();

    try {
        // Ambil pengguna yang sedang login
        $user = Auth::guard('masyarakat')->user();

        // Simpan nama tanpa memperhatikan validasi unik
        $user->nama = $validatedData['nama'];
        $user->save();

        // Simpan username tanpa memperhatikan validasi unik
        $user->username = $validatedData['username'];
        $user->save();

        // Simpan telepon tanpa memperhatikan validasi unik
        $user->telp = $validatedData['telp'];
        $user->save();

        // Commit transaksi
        DB::commit();

        // Redirect kembali ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui.');
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        DB::rollback();
        // Redirect kembali ke halaman profil dengan pesan error
        return redirect()->back()->with('error', 'Gagal memperbarui informasi profil. Silakan coba lagi.');
    }
}

public function changePassword(Request $request)
{
    // Validasi data yang dikirimkan
    $validatedData = $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:6|confirmed',
    ], [
        'current_password.required' => 'Masukkan password saat ini.',
        'password.required' => 'Masukkan password baru.',
        'password.min' => 'Password baru harus terdiri dari minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
    ]);

    // Ambil pengguna yang sedang login
    $user = Auth::guard('masyarakat')->user();

    // Periksa apakah password saat ini sesuai
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()->with('error', 'Password sekarang salah!!');
     } 
    //else {
    //     return redirect()->back()->with('error', 'Password baru dan konfirmasi password harus sama, serta harus terdiri dari minimal 6 karakter.');
    // }

    // Mulai transaksi database
    DB::beginTransaction();

    try {
        // Ubah password pengguna di database
        $user->password = Hash::make($request->password);
        $user->save();

        // Commit transaksi
        DB::commit();

        // Redirect kembali ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Password berhasil diubah.');
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        DB::rollback();
        // Redirect kembali ke halaman profil dengan pesan error
        return redirect()->back()->with('error', 'Gagal mengubah password. Silakan coba lagi.');
    }
}

public function uploadPhoto(Request $request)
{
    // Validasi foto yang diunggah
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // maksimal 2MB
    ]);

    // Ambil file foto dari request
    $photo = $request->file('photo');

    // Ambil user yang sedang login
    $user = Auth::guard('masyarakat')->user();

    // Simpan path foto profil lama sebelum diupdate
    $oldPhotoPath = $user->photo;

    // Simpan foto baru ke direktori public\storage\assets\fotoprofil
    $newPhotoPath = $photo->store('assets/fotoprofil', 'public');

    // Update path foto profil pengguna di database
    $user->photo = $newPhotoPath;
    $user->save();

    // Hapus foto lama dari penyimpanan jika ada
    if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
        Storage::disk('public')->delete($oldPhotoPath);
    }

    // Redirect kembali ke halaman profil dengan pesan sukses
    return redirect()->back()->with('success', 'Profile picture berhasil diupload.');
}

}
