<?php

    //=====================================================
    //  HOTEL MINI API CONFIG
    //  File: api/config.php
    //=====================================================
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, API-KEY");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    date_default_timezone_set("Asia/Ho_Chi_Minh");

    
    // |--------------------------------------------------------------------------
    // | Database
    // |--------------------------------------------------------------------------
    require_once __DIR__ . '/../config/database.php';

    if (!isset($conn) || !$conn) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Không thể kết nối cơ sở dữ liệu."
        ], JSON_UNESCAPED_UNICODE);

        exit();
    }

    mysqli_set_charset($conn, "utf8mb4");

   
    // |--------------------------------------------------------------------------
    // | Success Response
    // |--------------------------------------------------------------------------
    function success($data = [], $message = "Success", $code = 200)
    {
        http_response_code($code);

        echo json_encode([
            "success" => true,
            "message" => $message,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        exit();
    }


    // |--------------------------------------------------------------------------
    // | Error Response
    // |--------------------------------------------------------------------------
    function error($message = "Error", $code = 400, $errors = [])
    {
        http_response_code($code);

        echo json_encode([
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        exit();
    }

   
    // |--------------------------------------------------------------------------
    // | Read JSON Body
    // |--------------------------------------------------------------------------
    function getJson()
    {
        $raw = file_get_contents("php://input");

        if (empty($raw)) {
            return [];
        }

        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return is_array($data) ? $data : [];
    }

  
    // |--------------------------------------------------------------------------
    // | Escape String
    // |--------------------------------------------------------------------------
    function str($value)
    {
        global $conn;

        return mysqli_real_escape_string(
            $conn,
            trim((string)$value)
        );
    }


    // |--------------------------------------------------------------------------
    // | Integer
    // |--------------------------------------------------------------------------
    function num($value)
    {
        return intval($value);
    }


    // |--------------------------------------------------------------------------
    // | Decimal
    // |--------------------------------------------------------------------------
    function decimal($value)
    {
        return floatval($value);
    }


    // |--------------------------------------------------------------------------
    // | Validate Required Fields
    // |--------------------------------------------------------------------------
    function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (
                !isset($data[$field]) ||
                trim($data[$field]) === ""
            ) {
                error("Thiếu trường: " . $field);
            }
        }
    }


    // |--------------------------------------------------------------------------
    // | Generate Random Token
    // |--------------------------------------------------------------------------
    function generateToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    // |--------------------------------------------------------------------------
    // | Current Date Time
    // |--------------------------------------------------------------------------
    function now()
    {
        return date("Y-m-d H:i:s");
    }

?>