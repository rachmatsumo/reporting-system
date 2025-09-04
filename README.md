# Reporting System

<p>Merupakan sebuah sistem pelaporan berbasis web yang dapat disesuaikan dengan kebutuhan</p>

<h2>🚀 Instalasi</h2>
<ol>
    <li>Clone repository: <code>git clone https://github.com/rachmatsumo/hris-attendance.git report_system</code></li>
    <li>Buka directory: <code>cd report_system</code></li>
    <li>Jalankan: <code>composer install</code></li>
    <li>Salin file <code>.env.example</code> menjadi <code>.env</code></li>
    <li>Set konfigurasi environment di file <code>.env</code></li>
    <li>Generate app key: <code>php artisan key:generate</code></li>
    <li>Buat symbolic link: <code>php artisan storage:link</code></li>
    <li>Jalankan: <code>npm install</code></li>
    <li>Jalankan: <code>npm run build</code></li>
    <li>Jalankan migrasi database: <code>php artisan migrate</code></li> 
    <li>Jalankan server: <code>php artisan serve</code></li>
</ol>