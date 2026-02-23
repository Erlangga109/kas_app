// QRIS Functionality
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generate-qris');
    
    if (generateBtn) {
        generateBtn.addEventListener('click', function() {
            const nama = document.getElementById('qris-nama').value.trim();
            const jumlah = document.getElementById('qris-jumlah').value;
            const qrisDisplay = document.getElementById('qris-display');
            
            // Validasi input
            if (!nama) {
                alert('Nama pembayar harus diisi!');
                document.getElementById('qris-nama').focus();
                return;
            }
            
            if (!jumlah) {
                alert('Jumlah pembayaran harus dipilih!');
                document.getElementById('qris-jumlah').focus();
                return;
            }
            
            // Format jumlah ke Rupiah
            const jumlahFormatted = new Intl.NumberFormat('id-ID').format(jumlah);
            
            // Generate data untuk QR
            const qrData = `QRIS Payment\nNama: ${nama}\nJumlah: Rp ${jumlahFormatted}`;
            
            // Generate QR Code menggunakan library
            try {
                // Cek apakah library sudah dimuat
                if (typeof qrcode === 'undefined') {
                    alert('Library QR Code belum dimuat. Silakan refresh halaman.');
                    return;
                }
                
                const qr = qrcode(0, 'M');
                qr.addData(qrData);
                qr.make();
                
                // Tampilkan QR Code
                qrisDisplay.innerHTML = qr.createImgTag(8, 2);
                
                // Tambahkan informasi pembayaran
                qrisDisplay.innerHTML += `
                    <p style="margin-top: 15px; font-weight: bold;">${nama}</p>
                    <p style="color: #4CAF50; font-size: 18px;">Rp ${jumlahFormatted}</p>
                    <p style="font-size: 12px; color: #666;">Scan QR ini untuk pembayaran</p>
                `;
            } catch (error) {
                console.error('QRIS Error:', error);
                alert('Gagal generate QR Code: ' + error.message);
            }
        });
    }
});
