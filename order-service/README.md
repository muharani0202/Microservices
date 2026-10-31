# Microservices - E-Commerce

Project ini merupakan aplikasi sederhana berbasis **Microservices Architecture** yang terdiri dari beberapa service yang saling berkomunikasi untuk menjalankan proses transaksi e-commerce.

Project ini dibuat untuk memenuhi tugas perkuliahan sekaligus sebagai contoh penerapan arsitektur Microservices menggunakan Docker.

## 1. Persyaratan Sistem

Sebelum menjalankan project, pastikan perangkat kamu sudah memiliki:

- **Git** – untuk mengunduh source code dari GitHub.
- **Docker** – untuk menjalankan setiap service dalam container.
- **Docker Compose** – untuk mengelola beberapa container sekaligus.

Pastikan Docker sudah berjalan sebelum melanjutkan ke tahap berikutnya.

Untuk memeriksa instalasi Docker, jalankan perintah berikut di terminal:

```bash
docker --version
docker compose version
```

Jika kedua perintah menampilkan nomor versi, berarti Docker dan Docker Compose sudah terpasang.

## 2. Cara Download (Clone) Project

Ikuti langkah-langkah berikut untuk mendapatkan source code project.

### Langkah 1 - Buat Folder Project

Buat sebuah folder khusus di laptop kamu. Nama folder bebas, misalnya:

```bash
mkdir Project1
```

Masuk ke folder tersebut:

```bash
cd Project1
```

### Langkah 2 - Download Source Code

Jalankan perintah berikut untuk mengunduh project dari GitHub:

```bash
git clone https://github.com/muharani0202/Microservices.git
```

Tunggu sampai proses download selesai.

### Langkah 3 - Masuk ke Folder Project

Setelah berhasil di-download, masuk ke folder `Microservices`:

```bash
cd Microservices
```

### Langkah 4 - Jalankan Seluruh Service

Jalankan perintah berikut untuk membangun (build) image Docker dan menjalankan seluruh service:

```bash
sudo docker compose up -d --build
```

Tunggu sampai proses build selesai.

**Catatan:** Perintah tersebut sudah sekaligus menjalankan seluruh service dalam mode background. Kamu tidak perlu menjalankan `docker compose up -d` lagi setelahnya.

### Langkah 5 - Periksa Container

Untuk memastikan seluruh container sudah berjalan, gunakan:

```bash
sudo docker compose ps
```

Atau:

```bash
sudo docker ps
```

Jika berhasil, kamu akan melihat daftar container beserta statusnya.

Container yang berjalan dengan normal biasanya memiliki status `Up` atau `running`.

## 3. Perintah Docker yang Berguna

Berikut beberapa perintah Docker Compose yang dapat digunakan untuk mengelola service.

| Perintah | Fungsi |
|---|---|
| `docker compose up -d` | Menjalankan seluruh service. |
| `docker compose up -d --build` | Build ulang image dan menjalankan seluruh service. |
| `docker compose up -d --build order-service` | Build dan menjalankan order-service. |
| `docker compose stop` | Menghentikan seluruh service tanpa menghapus container. |
| `docker compose stop product-service` | Menghentikan product-service saja. |
| `docker compose start cart-service` | Menjalankan kembali cart-service yang sebelumnya dihentikan. |
| `docker compose ps` | Melihat daftar service dan statusnya. |
| `docker compose logs -f order-service` | Melihat log Order Service secara langsung. |

**Catatan:** Pada Linux, jika Docker membutuhkan hak akses administrator, tambahkan `sudo` di depan perintah.

## 4. Informasi Pengujian API

Pengujian API dapat dilakukan menggunakan aplikasi seperti:

- Postman
- Insomnia
- Thunder Client (ekstensi Visual Studio Code)

Sebelum melakukan pengujian, atur HTTP Headers berikut:

| Key | Value |
|---|---|
| Content-Type | application/json |
| Accept | application/json |

### Daftar Service dan Port

| Service | Port | Fungsi |
|---|---:|---|
| Product Service | 3000 | Mengelola data produk. |
| Cart Service | 3001 | Mengelola keranjang belanja. |
| Order Service | 3003 | Mengelola pesanan. |

### Penting: Memahami ID Dinamis

Pada dokumentasi ini terdapat beberapa contoh ID, seperti `{user_id}`, `{product_id}`, dan `{order_id}`.

ID tersebut merupakan **placeholder (penanda sementara)**, bukan angka yang harus dikirim secara langsung.

Kamu harus menggantinya dengan ID yang tersedia di database atau ID yang diperoleh dari response API sebelumnya.

Contoh:

```text
{user_id}    = 7
{product_id} = 1
{order_id}   = 8
```

Jika pada perangkat kamu mendapatkan ID pengguna 10, maka ganti `{user_id}` menjadi `10`.

Begitu juga dengan ID produk dan ID pesanan. Selalu gunakan ID sesuai data yang tersedia pada project kamu.

**Perhatian:** Jangan mengetik tanda `{}` ketika mengirim request. Gantilah seluruh placeholder dengan nilai ID sebenarnya.

---

## 5. Pengujian Product Service

Product Service digunakan untuk mengambil informasi produk yang tersedia.

### 5.1 Mengambil Data Produk

**Method:** GET

**URL:**

```http
http://localhost:3000/products/{product_id}
```

Contoh jika ID produk adalah `1`:

```http
http://localhost:3000/products/1
```

**Response: HTTP 200 OK**

```json
{
  "data": {
    "id": 1,
    "name": "Keyboard mekanikal",
    "price": "150000.00",
    "description": "Deskripsi untuk Keyboard Mekanikal",
    "stock": 10,
    "image": "R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",
    "created_at": "2026-10-24T13:14:23.000Z",
    "updated_at": "2026-10-24T13:14:23.000Z"
  }
}
```

### 5.2 Mengambil Data Produk Lain

**Method:** GET

**URL:**

```http
http://localhost:3000/products/{product_id}
```

Contoh jika ID produk adalah `2`:

```http
http://localhost:3000/products/2
```

**Response: HTTP 200 OK**

```json
{
  "data": {
    "id": 2,
    "name": "Mouse gaming",
    "price": "75000.00",
    "description": "Deskripsi untuk Mouse Gaming",
    "stock": 20,
    "image": "R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",
    "created_at": "2026-10-24T13:14:23.000Z",
    "updated_at": "2026-10-24T13:14:23.000Z"
  }
}
```

**Catatan:** Data produk di atas hanya contoh. Hasil yang muncul pada perangkat kamu dapat berbeda tergantung isi database.

## 6. Pengujian Cart Service

Cart Service bertugas mengelola keranjang belanja pengguna.

### 6.1 Mengambil Keranjang Belanja

**Method:** GET

**URL:**

```http
http://localhost:3001/carts/{user_id}
```

Contoh:

```http
http://localhost:3001/carts/7
```

**Response: HTTP 200 OK**

```json
{
  "message": "Berhasil mengambil keranjang",
  "data": {
    "user_id": 7,
    "items": [],
    "total_quantity": 0
  }
}
```

Response tersebut menunjukkan bahwa pengguna dengan ID tersebut belum memiliki produk di keranjang belanja.

### 6.2 Menambahkan Produk ke Keranjang

**Method:** POST

**URL:**

```http
http://localhost:3001/carts
```

**Body (JSON):**

```json
{
  "user_id": 7,
  "product_id": 1,
  "quantity": 2
}
```

Sesuaikan nilai `user_id` dan `product_id` dengan ID yang tersedia pada database kamu.

Keterangan:

- `user_id`: ID pengguna yang akan menambahkan produk.
- `product_id`: ID produk yang ingin dibeli.
- `quantity`: Jumlah produk yang ingin dibeli.

**Response: HTTP 201 Created**

```json
{
  "message": "Item berhasil ditambahkan ke keranjang",
  "data": {
    "user_id": 7,
    "product_id": 1,
    "quantity": 2,
    "updated_at": "2026-10-30T19:59:31.000000Z",
    "created_at": "2026-10-30T19:59:31.000000Z",
    "id": 9
  }
}
```

Artinya, produk berhasil ditambahkan ke keranjang pengguna.

## 7. Pengujian Order Service

Order Service bertugas memproses pesanan berdasarkan isi keranjang belanja pengguna.

### 7.1 Memeriksa Health Service

Untuk memastikan Order Service berjalan dengan normal, lakukan pengujian berikut.

**Method:** GET

**URL:**

```http
http://localhost:3003/health
```

**Response: HTTP 200 OK**

```json
{
  "status": "ok",
  "service": "order-service"
}
```

Jika mendapatkan response tersebut, berarti Order Service berhasil dijalankan.

### 7.2 Menguji Validasi Data Pesanan

Lakukan pengujian tanpa mengirimkan data `user_id`.

**Method:** POST

**URL:**

```http
http://localhost:3003/orders
```

**Body (JSON):**

```json
{}
```

**Response: HTTP 422 Unprocessable Entity**

```json
{
  "message": "Field user_id wajib diisi",
  "errors": {
    "user_id": [
      "Field user_id wajib diisi"
    ]
  }
}
```

Response tersebut menunjukkan bahwa sistem berhasil melakukan validasi dan menolak request karena `user_id` belum diisi.

### 7.3 Menguji Pesanan dengan Keranjang Kosong

Selanjutnya, kirimkan request dengan `user_id` yang valid.

**Method:** POST

**URL:**

```http
http://localhost:3003/orders
```

**Body (JSON):**

```json
{
  "user_id": 7
}
```

**Response: HTTP 409 Conflict**

```json
{
  "message": "Keranjang belanja kosong"
}
```

Response tersebut menunjukkan bahwa pesanan tidak dapat dibuat karena keranjang pengguna masih kosong.

## 8. Pengujian Penanganan Error 503 - Service Unavailable

Pengujian ini bertujuan untuk memastikan Order Service dapat menangani kondisi ketika Cart Service tidak dapat diakses.

### Langkah 1 - Hentikan Cart Service

Buka terminal pada folder project yang berisi file `docker-compose.yml`.

Jalankan:

```bash
docker compose stop cart-service
```

### Langkah 2 - Coba Membuat Pesanan

**Method:** POST

**URL:**

```http
http://localhost:3003/orders
```

**Body (JSON):**

```json
{
  "user_id": 7
}
```

**Response: HTTP 503 Service Unavailable**

```json
{
  "message": "Koneksi ke Cart Service gagal"
}
```

Response tersebut menunjukkan bahwa Order Service berhasil mendeteksi kegagalan komunikasi dengan Cart Service.

### Langkah 3 - Hidupkan Kembali Cart Service

Setelah pengujian selesai, jalankan:

```bash
docker compose start cart-service
```

Periksa status seluruh service:

```bash
docker compose ps
```

Pastikan Cart Service sudah berjalan kembali.

## 9. Pengujian Error 500 - Internal Server Error

Pengujian ini bertujuan untuk mengetahui bagaimana Order Service menangani kesalahan internal pada server.

**Perhatian:** Pengujian ini memerlukan perubahan kode sementara. Pastikan kamu mengembalikan kode seperti semula setelah pengujian selesai.

### Langkah 1 - Buka File OrderController.php

Buka file berikut menggunakan editor kode:

```text
order-service/app/Http/Controllers/OrderController.php
```

### Langkah 2 - Ubah Kode Sementara

Cari baris ke-32 dan ke-34, kemudian lakukan perubahan kode sesuai skenario simulasi error internal server yang disediakan dalam project.

### Langkah 3 - Build Ulang Order Service

```bash
docker compose up -d --build order-service
```

Tunggu sampai proses build selesai.

### Langkah 4 - Lakukan Pengujian

**Method:** POST

**URL:**

```http
http://localhost:3003/orders
```

**Body (JSON):**

```json
{
  "user_id": 7
}
```

**Response: HTTP 500 Internal Server Error**

```json
{
  "message": "Terjadi kesalahan pada server",
  "error": "Simulasi kesalahan internal server"
}
```

Response tersebut menunjukkan bahwa aplikasi berhasil menangani simulasi kesalahan internal server.

### Langkah 5 - Kembalikan Kode Seperti Semula

Setelah pengujian selesai, kembalikan perubahan pada file `OrderController.php` seperti kondisi awal.

Kemudian build ulang service:

```bash
docker compose up -d --build order-service
```

**Penting:** Jangan biarkan kode simulasi error tetap digunakan karena dapat menyebabkan proses pembuatan pesanan gagal.

## 10. Pengujian Pembuatan Pesanan Berhasil (201 Created)

Sebelum membuat pesanan, pastikan pengguna sudah memiliki produk di dalam keranjang.

Jika belum, tambahkan produk terlebih dahulu menggunakan Cart Service pada bagian 6.

### Langkah 1 - Buat Pesanan

**Method:** POST

**URL:**

```http
http://localhost:3003/orders
```

**Body (JSON):**

```json
{
  "user_id": 7
}
```

Ganti angka `7` dengan ID pengguna yang sama dengan pengguna yang sebelumnya digunakan untuk menambahkan produk ke keranjang.

**Response: HTTP 201 Created**

```json
{
  "message": "Pesanan berhasil dibuat",
  "data": {
    "user_id": 7,
    "status": "PENDING",
    "total_amount": 300000,
    "updated_at": "2026-10-30T19:02:06.000000Z",
    "created_at": "2026-10-30T19:02:06.000000Z",
    "id": 8,
    "items": [
      {
        "id": 8,
        "order_id": 8,
        "product_id": 1,
        "product_name": "Keyboard mekanikal",
        "unit_price": "150000.00",
        "quantity": 2,
        "subtotal": "300000.00",
        "created_at": "2026-10-30T19:02:06.000000Z",
        "updated_at": "2026-10-30T19:02:06.000000Z"
      }
    ]
  }
}
```

Pesanan berhasil dibuat dengan status `PENDING`.

Artinya, pesanan sudah tersimpan, tetapi belum dibayar.

**PENTING:**

Perhatikan nilai `id` pada bagian `data` response di atas.

Contoh:

```json
{
  "id": 8
}
```

Angka `8` merupakan ID pesanan yang baru saja dibuat.

Jika pada perangkat kamu mendapatkan ID `15`, maka gunakan ID `15` untuk pengujian selanjutnya.

Jangan terpaku pada angka yang ada di dokumentasi ini.

### Langkah 2 - Mengambil Detail Pesanan

Gunakan ID pesanan yang diperoleh dari response sebelumnya.

**Method:** GET

**URL:**

```http
http://localhost:3003/orders/{order_id}
```

Contoh jika ID pesanan yang diperoleh adalah `8`:

```http
http://localhost:3003/orders/8
```

**Response: HTTP 200 OK**

```json
{
  "message": "Berhasil mengambil pesanan",
  "data": {
    "id": 8,
    "user_id": 7,
    "status": "PENDING",
    "total_amount": "300000.00",
    "created_at": "2026-10-30T19:02:06.000000Z",
    "updated_at": "2026-10-30T19:02:06.000000Z",
    "items": [
      {
        "id": 8,
        "order_id": 8,
        "product_id": 1,
        "product_name": "Keyboard mekanikal",
        "unit_price": "150000.00",
        "quantity": 2,
        "subtotal": "300000.00",
        "created_at": "2026-10-30T19:02:06.000000Z",
        "updated_at": "2026-10-30T19:02:06.000000Z"
      }
    ]
  }
}
```

### Langkah 3 - Menguji Pesanan yang Tidak Ditemukan

Untuk menguji penanganan pesanan yang tidak tersedia, gunakan ID yang tidak ada di database.

**Method:** GET

**URL:**

```http
http://localhost:3003/orders/{order_id}
```

Contoh menggunakan ID `99` sebagai ID yang diasumsikan tidak tersedia:

```http
http://localhost:3003/orders/99
```

**Response: HTTP 404 Not Found**

```json
{
  "message": "Pesanan dengan id 99 tidak ditemukan"
}
```

Catatan: Pastikan ID yang digunakan benar-benar tidak tersedia di database kamu. Jika ID tersebut ternyata ada, gunakan ID lain yang belum digunakan.

## 11. Mengambil Daftar Pesanan Berdasarkan User ID

Order Service juga menyediakan fitur untuk mengambil seluruh pesanan berdasarkan ID pengguna.

### 11.1 Mengambil Seluruh Pesanan Pengguna

**Method:** GET

**URL:**

```http
http://localhost:3003/orders/user/{user_id}
```

Contoh:

```http
http://localhost:3003/orders/user/7
```

### 11.2 Mengambil Pesanan Berdasarkan Status PENDING

**Method:** GET

**URL:**

```http
http://localhost:3003/orders/user/{user_id}?status=PENDING
```

Contoh:

```http
http://localhost:3003/orders/user/7?status=PENDING
```

Request tersebut digunakan untuk mengambil pesanan pengguna yang memiliki status `PENDING`.

### 11.3 Menguji Validasi Status Pesanan

**Method:** GET

**URL:**

```http
http://localhost:3003/orders/user/{user_id}?status=SELESAI
```

Contoh:

```http
http://localhost:3003/orders/user/7?status=SELESAI
```

**Response: HTTP 422 Unprocessable Entity**

```json
{
  "message": "Data yang dikirim tidak valid",
  "errors": {
    "status": [
      "Status harus PENDING atau PAID"
    ]
  }
}
```

Response tersebut menunjukkan bahwa sistem hanya menerima filter status `PENDING` atau `PAID`.

## 12. Pengujian Pembayaran Pesanan

Fitur pembayaran digunakan untuk mengubah status pesanan dari `PENDING` menjadi `PAID`.

### 12.1 Melakukan Pembayaran

Pastikan pesanan sudah berhasil dibuat dan masih memiliki status `PENDING`.

Gunakan `{order_id}` yang diperoleh ketika membuat pesanan pada bagian 10.

**Method:** PATCH

**URL:**

```http
http://localhost:3003/orders/{order_id}/pay
```

Contoh jika ID pesanan adalah `8`:

```http
http://localhost:3003/orders/8/pay
```

**Response: HTTP 200 OK**

```json
{
  "message": "Pesanan berhasil dibayar",
  "data": {
    "id": 8,
    "user_id": 7,
    "status": "PAID",
    "total_amount": "300000.00",
    "created_at": "2026-10-30T19:02:06.000000Z",
    "updated_at": "2026-10-30T20:24:06.000000Z",
    "items": [
      {
        "id": 8,
        "order_id": 8,
        "product_id": 1,
        "product_name": "Keyboard mekanikal",
        "unit_price": "150000.00",
        "quantity": 2,
        "subtotal": "300000.00",
        "created_at": "2026-10-30T19:02:06.000000Z",
        "updated_at": "2026-10-30T19:02:06.000000Z"
      }
    ]
  }
}
```

Jika berhasil, status pesanan akan berubah dari `PENDING` menjadi `PAID`.

### 12.2 Menguji Pembayaran dengan ID yang Tidak Ditemukan

**Method:** PATCH

**URL:**

```http
http://localhost:3003/orders/{order_id}/pay
```

Contoh:

```http
http://localhost:3003/orders/99/pay
```

Pastikan ID tersebut memang tidak tersedia di database.

**Response: HTTP 404 Not Found**

```json
{
  "message": "Pesanan dengan id 99 tidak ditemukan"
}
```

### 12.3 Menguji Pembayaran Pesanan yang Sudah Dibayar

Coba lakukan pembayaran kembali terhadap pesanan yang sebelumnya sudah berhasil dibayar.

**Method:** PATCH

**URL:**

```http
http://localhost:3003/orders/{order_id}/pay
```

Gunakan ID pesanan yang sama dengan pengujian pembayaran sebelumnya.

**Response: HTTP 409 Conflict**

```json
{
  "message": "Pesanan berstatus PAID, hanya pesanan PENDING yang dapat dibayar"
}
```

Response tersebut menunjukkan bahwa sistem mencegah pembayaran berulang terhadap pesanan yang sudah memiliki status `PAID`.

## 13. Ringkasan HTTP Status Code

Berikut ringkasan status code yang digunakan dalam pengujian project ini.

| HTTP Status | Keterangan | Penjelasan |
|---|---|---|
| 200 | OK | Request berhasil diproses. |
| 201 | Created | Data berhasil dibuat. |
| 404 | Not Found | Data atau resource tidak ditemukan. |
| 409 | Conflict | Request bertentangan dengan kondisi data saat ini. |
| 422 | Unprocessable Entity | Data yang dikirim tidak memenuhi validasi. |
| 500 | Internal Server Error | Terjadi kesalahan internal pada server. |
| 503 | Service Unavailable | Service yang dibutuhkan sedang tidak tersedia. |

## 14. Melihat Log Error

Jika terjadi masalah ketika Order Service berkomunikasi dengan Product Service, kamu bisa memeriksa log aplikasi menggunakan perintah berikut:

```bash
docker compose exec order-service sh -c "grep -i 'ProductClient' storage/logs/laravel.log"
```

Perintah tersebut digunakan untuk mencari baris log yang mengandung kata `ProductClient` pada file log Laravel di dalam container Order Service.

Untuk melihat log secara langsung:

```bash
docker compose logs -f order-service
```

Tekan `Ctrl + C` untuk berhenti melihat log.

## 15. Menghentikan Seluruh Service

Jika sudah selesai melakukan pengujian, kamu bisa menghentikan seluruh service menggunakan:

```bash
docker compose stop
```

Perintah ini hanya menghentikan container. Data dan container tidak dihapus.

Jika ingin menjalankan kembali seluruh service:

```bash
docker compose start
```

---

## 16. Penutup

Project ini mendemonstrasikan penerapan arsitektur Microservices dengan beberapa service yang memiliki tanggung jawab masing-masing.

Melalui project ini, pengguna dapat mempelajari:

- Cara menjalankan beberapa service menggunakan Docker Compose.
- Komunikasi antar-service dalam arsitektur Microservices.
- Pengelolaan produk, keranjang belanja, dan pesanan.
- Validasi request menggunakan HTTP status code.
- Penanganan error ketika service lain tidak tersedia.
- Simulasi Internal Server Error.
- Proses pembuatan dan pembayaran pesanan.

**Terima kasih telah mengunjungi repository ini. Semoga project ini bermanfaat sebagai bahan pembelajaran mengenai Microservices Architecture.**
