# Web4s Docker Development Environment

## Tổng quan
Dự án này cung cấp môi trường Docker để phát triển các version Web4s khác nhau với các version PHP tương ứng.

> **Lưu ý**: Từ phiên bản này, dự án đã chuyển từ MySQL sang MariaDB 10.5.9 để có hiệu suất tốt hơn và tương thích tốt với các version PHP mới.

## Các version được hỗ trợ
- **Web4s 4.0.0**: PHP 5.6 + MariaDB 10.5.9
- **Web4s 5.0.0**: PHP 7.4 + MariaDB 10.5.9  
- **Web4s 5.4.0**: PHP 8.1 + MariaDB 10.5.9

## Cấu trúc thư mục
```
docker-tutorial/
├── docker-compose.yml          # File compose chính
├── dockerfiles/                # Thư mục chứa Dockerfile
│   ├── php5.6/                 # Dockerfile cho PHP 5.6
│   ├── php7.4/                 # Dockerfile cho PHP 7.4
│   └── php8.1/                 # Dockerfile cho PHP 8.1
├── configs/                    # Cấu hình cho từng version
│   ├── web4s-4.0.0/
│   ├── web4s-5.0.0/
│   └── web4s-5.4.0/
└── scripts/                    # Scripts tiện ích
    ├── start-web4s-4.0.0.sh
    ├── start-web4s-5.0.0.sh
    └── start-web4s-5.4.0.sh
```

## Cách sử dụng
1. Clone dự án này
2. Copy source code Web4s vào thư mục tương ứng
3. Chạy lệnh Docker Compose cho version cần thiết
4. Truy cập ứng dụng qua localhost

## Yêu cầu hệ thống
- Docker Desktop (Windows/Mac) hoặc Docker Engine (Linux)
- Docker Compose
- Git 