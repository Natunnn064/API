<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf8');
$koneksi = mysqli_connect("localhost", "root", "", "data_users");

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API Users",
 *         version="1.0.0",
 *         description="Dokumentasi API untuk mengelola data user"
 *     ),
 *     @OA\Server(
 *         url="http://localhost/PAT",
 *         description="Server lokal"
 *     )
 * )
 */


// Cek metode request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        /**
         * @OA\Get(
         *     path="/API.php/{id}",
         *     summary="Mengambil data pengguna berdasarkan ID",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Data pengguna berhasil diambil",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="id", type="integer"),
         *             @OA\Property(property="nama", type="string"),
         *             @OA\Property(property="jenis_kelamin", type="string"),
         *             @OA\Property(property="email", type="string")
         *         )
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Data tidak ditemukan"
         *     )
         * )
         */
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM users WHERE id = $id";
        $query = mysqli_query($koneksi, $sql);
        
        if ($data = mysqli_fetch_assoc($query)) {
            echo json_encode($data); // Mengembalikan data pengguna jika ditemukan
        } else {
            echo json_encode(["status" => "data tidak ditemukan"]); // Pesan jika data tidak ditemukan
        }
    } else {
        /**
         * @OA\Get(
         *     path="/API.php",
         *     summary="Mengambil semua data pengguna",
         *     @OA\Response(
         *         response=200,
         *         description="Daftar pengguna berhasil diambil",
         *         @OA\JsonContent(
         *             type="array",
         *             @OA\Items(
         *                 @OA\Property(property="id", type="integer"),
         *                 @OA\Property(property="nama", type="string"),
         *                 @OA\Property(property="jenis_kelamin", type="string"),
         *                 @OA\Property(property="email", type="string")
         *             )
         *         )
         *     )
         * )
         */
        $sql = "SELECT * FROM users";
        $query = mysqli_query($koneksi, $sql);
        $array_data = array();
        while ($data = mysqli_fetch_assoc($query)) {
            $array_data[] = $data;
        }
        echo json_encode($array_data); // Mengembalikan semua data pengguna
    }
} 
    /**
     * @OA\Post(
     *     path="/API.php",
     *     summary="Menambahkan pengguna baru",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama", "jenis_kelamin", "email"},
     *             @OA\Property(property="nama", type="string"),
     *             @OA\Property(property="jenis_kelamin", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pengguna berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="berhasil")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Gagal menambahkan pengguna"
     *     )
     * )
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
    
        if (isset($data['nama'], $data['jenis_kelamin'], $data['email'])) {
            $nama = $data['nama'];
            $jenis_kelamin = $data['jenis_kelamin'];
            $email = $data['email'];
    
            $sql = "INSERT INTO users (nama, jenis_kelamin, email) 
                    VALUES ('$nama', '$jenis_kelamin', '$email')";
            $cek = mysqli_query($koneksi, $sql);
    
            if ($cek) {
                echo json_encode(['status' => "berhasil"]);
            } else {
                echo json_encode(['status' => "gagal"]);
            }
        } else {
            echo json_encode(['status' => "gagal", 'message' => "Data tidak lengkap"]);
        }
    }