<?php

namespace App\Core;

class ApiResponse {
    private $data;
    private string $statut;
    private int $code;
    private string $message;

    public function __construct($data, string $statut, int $code, string $message) {
        $this->data = $data;
        $this->statut = $statut;
        $this->code = $code;
        $this->message = $message;
    }

    public static function success($data, string $message = "Opération réussie", int $code = 200): self {
        return new self($data, "success", $code, $message);
    }

    public static function error(string $message, int $code = 500, $data = null): self {
        return new self($data, "error", $code, $message);
    }

    public function toArray(): array {
        return [
            'data' => $this->data,
            'statut' => $this->statut,
            'code' => $this->code,
            'message' => $this->message
        ];
    }

    public function toJson(): string {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function send(): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($this->code);
        echo $this->toJson();
        exit();
    }

    // Getters
    public function getData() {
        return $this->data;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function getCode(): int {
        return $this->code;
    }

    public function getMessage(): string {
        return $this->message;
    }
}