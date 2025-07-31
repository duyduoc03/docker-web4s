# Script dừng tất cả các container Web4s
Write-Host "Dừng tất cả các container Web4s..." -ForegroundColor Red

docker-compose -f docker-compose.web4s-4.0.0.yml down
docker-compose -f docker-compose.web4s-5.0.0.yml down
docker-compose -f docker-compose.web4s-5.4.0.yml down

Write-Host "Tất cả các container đã được dừng!" -ForegroundColor Green 