<!-- === DOKUMENTASI KLASIFIKASI FILE === -->
<!-- -> Nama File: index.php (dalam folder auth) -->
<!-- -> Tujuan Spesifik: Antarmuka UI Login Page / Halaman tempat User memasukkan Kredensial untuk masuk ke sistem -->
<!-- ====================================== -->

<!-- [SINTAKS HTML]: <!DOCTYPE html> | Mendeklarasikan bahwa struktur dokumen ini menggunakan standar HTML5 modern -->
<!DOCTYPE html>
<!-- [SINTAKS HTML]: <html lang="id"> | Tag akar / Root HTML dengan pengaturan bahasa indonesia untuk mempermudah SEO/mesin pencari -->
<html lang="id">
<head>
    <!-- [SINTAKS HTML]: <meta charset="UTF-8"> | Mengatur karakter tata bahasa UTF-8 agar browser mengenali emoji dan simbol-simbol unik -->
    <meta charset="UTF-8">
    <!-- [SINTAKS HTML]: <meta name="viewport"> | Parameter khusus (Mobile-First) agar lebar layout menyesuaikan layar HP/Tablet tanpa harus di-zoom manual -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- [SINTAKS HTML]: <title> | Menentukan nama judul yang tampil pada tabulasi browser pengguna saat itu -->
    <title>Login - Hogwarts Parking</title>
    
    <!-- [SINTAKS HTML]: <link> Google Fonts | Menarik / mengunduh aset font tipografi "Plus Jakarta Sans" eksternal dengan ketebalan standar (400) sampai tebal ekstra (800) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- [SINTAKS CSS]: <style> | Menulis kode logika gaya desain murni Internal CSS -->
    <style>
        /* [SINTAKS CSS]: :root | Deklarator variabel CSS global (Tokens) yang berfungsi seperti variabel PHP tapi untuk menyimpan warna hex */
        :root {
            --primary-blue: #2563eb; 
            --royal-blue: #1e40af;
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
            --text-main: #1e293b;
            --white: #ffffff;
        }

        /* [SINTAKS CSS]: Universal Selector (*) | Mereset segala bawaan spacing browser agar mempermudah perhitungan grid flexbox margin padding */
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* [SINTAKS CSS]: body selector | Menargetkan punggung / elemen background paling utama dari dokumen website */
        body { 
            margin: 0; 
            background: var(--bg-gradient); 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh;
        }

        /* [SINTAKS CSS]: .login-container | Kotak putih yang membungkus gambar dan form secara horizontal-flex */
        .login-container {
            width: 100%; max-width: 900px; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
            margin: 20px;
        }

        /* [SINTAKS CSS]: .brand-side | Bagian sebelah kiri yang menjadi panggung untuk logo ilustrasi */
        .brand-side {
            flex: 1; background: #f1f5f9;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 60px; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* [SINTAKS CSS]: target children elem | Memberi aturan layout khusus pada anak / turunan dari bungkusnya */
        .brand-side img { width: 180px; margin-bottom: 25px; }
        .brand-side h2 { color: #0f172a; font-weight: 800; margin: 0; font-size: 24px; }
        .brand-side p { color: #475569; font-size: 14px; margin-top: 10px; }

        /* [SINTAKS CSS]: .form-side | Sisi sebelah kanan ruang pengetikan form text password dan tombol Submit */
        .form-side {
            flex: 1; padding: 60px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .form-side h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .form-side p { color: #475569; font-size: 14px; margin-bottom: 35px; }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        
        /* [SINTAKS CSS]: input text | Tata panggung dekoratif desain kotak-kotak pengetikan keyboard (Rounded) */
        .input-group input {
            width: 100%; padding: 14px 20px; border-radius: 16px;
            border: 2px solid #f1f5f9; background: #f1f5f9;
            font-size: 14px; color: #0f172a; transition: 0.3s;
        }

        /* [SINTAKS CSS]: :focus pseudo-class | Efek bersinar (Glow Ring Shadow) otomatis saat salah satu form/kolom input diklik menggunakan kursor */
        .input-group input:focus {
            outline: none; border-color: #2563eb; background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* [SINTAKS CSS]: button modifier | Customisasi tombol submit berwarna solid gradient / royal blue */
        .btn-login {
            width: 100%; padding: 16px; border-radius: 16px;
            background: #2563eb; color: white; border: none;
            font-size: 16px; font-weight: 700; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        /* [SINTAKS CSS]: :hover | Menambahkan transisi ke atas (Elevation Y-Axis) saat tombol didekati kursor mouse (Animasi halimun) */
        .btn-login:hover { background: var(--royal-blue); transform: translateY(-2px); }

        /* [SINTAKS CSS]: @media (max-width) | Breakpoints CSS / Desain Responsif jika situs ini dibuka di layar perangkat genggam (Mobile) di bawah 768px */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; max-width: 450px; }
            .brand-side { padding: 40px; border-right: none; border-bottom: 1px solid #f1f5f9; }
            .form-side { padding: 40px; }
        }
    </style>
</head>
<body>

    <!-- [SINTAKS HTML]: <div> Container utama flex -->
    <div class="login-container">
        <!-- [SINTAKS HTML]: <div> Pembungkus Sisi Kiri / Logo Promo Parkir -->
        <div class="brand-side">
            <!-- [SINTAKS HTML]: <img> | Memuat / merender aset visual logo (path ke gambar di folder public) -->
            <img src="../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>PARLINE</h2>
            <p>Smart Parking Solution</p>
        </div>

        <!-- [SINTAKS HTML]: <div> Pembungkus sisi kanan / Mekanisme Forms -->
        <div class="form-side">
            <h1>Selamat Datang</h1>
            <p>Silakan login untuk mengelola Website Parline</p>

            <!-- [SINTAKS HTML]: <form> | Menyalakan Mode Formulir Pengiriman. 
                 -> action="..." : Mengirim / Melemparkan data pengetikan ke script "proses_login.php"
                 -> method="POST" : Moda pengiriman tersembunyi ber-enkripsi, tidak nampak URL parameter (Secure Method) -->
            <form action="proses_login.php" method="POST">
                
                <div class="input-group">
                    <label>Username</label>
                    <!-- [SINTAKS HTML]: <input type="text"> Kolom data text polos Username (Name="username" menjadi penanda key Array $_POST nantinya) -->
                    <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <!-- [SINTAKS HTML]: <input type="password"> Kolom ketikan Sandi / akan otomatis menjadi titik-titik bulat rahasia yang tidak bisa dibaca kasat mata -->
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <!-- [SINTAKS HTML]: <button type="submit"> Tombol pemicu mekanika Action-Submit untuk melempar kumpulan seluruh data form ini -->
                <button type="submit" name="login" class="btn-login">Masuk ke Sistem</button>
            </form>
        </div>
    </div>

</body>
</html>
