# Script khởi động Web4s 5.0.0 với PHP 7.4
Write-Host "Khởi động Web4s 5.0.0 (PHP 7.4)..." -ForegroundColor Green

# Dừng các container khác nếu đang chạy
docker-compose -f docker-compose.web4s-4.0.0.yml down
docker-compose -f docker-compose.web4s-5.4.0.yml down

# Khởi động Web4s 5.0.0
docker-compose -f docker-compose.web4s-5.0.0.yml up -d --build

Write-Host "Web4s 5.0.0 đã được khởi động!" -ForegroundColor Green
Write-Host "Website: http://localhost:8082" -ForegroundColor Yellow
Write-Host "PhpMyAdmin: http://localhost:8083" -ForegroundColor Yellow
Write-Host "Database: localhost:3307" -ForegroundColor Yellow 