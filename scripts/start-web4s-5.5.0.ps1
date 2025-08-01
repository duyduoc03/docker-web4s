# Script khởi động Web4s v5.5.0 với PHP 8.1
Write-Host "Khởi động Web4s v5.5.0 (PHP 8.1)..." -ForegroundColor Green

# Dừng các container khác nếu đang chạy
docker-compose -f docker-compose.web4s-4.0.0.yml down
docker-compose -f docker-compose.web4s-5.0.0.yml down
docker-compose -f docker-compose.web4s-5.4.0.yml down

# Khởi động Web4s v5.5.0
docker-compose -f docker-compose.web4s-5.5.0.yml up -d --build

Write-Host "Web4s 5.5.0 đã được khởi động!" -ForegroundColor Green
Write-Host "Website: http://localhost:8084" -ForegroundColor Yellow
Write-Host "PhpMyAdmin: http://localhost:8085" -ForegroundColor Yellow
Write-Host "Database: localhost:3308" -ForegroundColor Yellow 