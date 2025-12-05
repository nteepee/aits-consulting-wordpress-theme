#!/bin/bash

# AITS Consulting Theme - Docker Test Environment Startup Script

echo "======================================"
echo "AITS Consulting Theme - Docker Setup"
echo "======================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed!"
    echo "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop"
    exit 1
fi

echo "✅ Docker found: $(docker --version)"
echo ""

# Check if Docker daemon is running
if ! docker ps &> /dev/null; then
    echo "⚠️  Docker daemon is not running!"
    echo ""
    echo "On macOS:"
    echo "  1. Open 'Docker Desktop' from Applications"
    echo "  2. Wait for Docker to start"
    echo "  3. Then run this script again"
    echo ""
    exit 1
fi

echo "✅ Docker daemon is running"
echo ""

# Check port availability
echo "Checking ports..."
if lsof -i :8081 &> /dev/null; then
    echo "⚠️  Port 8081 (WordPress) is in use"
    echo "   Kill existing process: lsof -i :8081 | tail -1 | awk '{print \$2}' | xargs kill -9"
    exit 1
fi

if lsof -i :3307 &> /dev/null; then
    echo "⚠️  Port 3307 (MySQL) is in use"
    echo "   Kill existing process: lsof -i :3307 | tail -1 | awk '{print \$2}' | xargs kill -9"
    exit 1
fi

if lsof -i :8082 &> /dev/null; then
    echo "⚠️  Port 8082 (PHPMyAdmin) is in use"
    echo "   Kill existing process: lsof -i :8082 | tail -1 | awk '{print \$2}' | xargs kill -9"
    exit 1
fi

echo "✅ All required ports available (8081, 3307, 8082)"
echo ""

# Start Docker containers
echo "Starting Docker containers..."
echo "  - MySQL 8.0"
echo "  - WordPress 6.4 with PHP 8.2"
echo "  - PHPMyAdmin"
echo ""

docker compose up -d

if [ $? -ne 0 ]; then
    echo "❌ Failed to start Docker containers"
    exit 1
fi

echo "✅ Containers started!"
echo ""

# Wait for services to be ready
echo "Waiting for services to initialize (this may take 30-60 seconds)..."
echo ""

for i in {1..60}; do
    if docker compose exec -T wordpress curl -s http://localhost/ &> /dev/null; then
        echo "✅ WordPress is ready!"
        break
    fi
    echo -n "."
    sleep 1
done

echo ""
echo ""
echo "======================================"
echo "✅ Docker Setup Complete!"
echo "======================================"
echo ""
echo "📍 URLS:"
echo "  WordPress:  http://localhost:8081"
echo "  PHPMyAdmin: http://localhost:8082"
echo ""
echo "🔐 Database Credentials:"
echo "  Host:     localhost (or mysql:3306 from containers)"
echo "  Port:     3307"
echo "  Database: wordpress_db"
echo "  User:     wordpress_user"
echo "  Password: wordpress_pass_123"
echo ""
echo "📚 Next Steps:"
echo "  1. Open http://localhost:8081 in your browser"
echo "  2. Complete WordPress initial setup"
echo "  3. Go to Appearance → Themes"
echo "  4. Activate 'Stitch Consulting Theme'"
echo "  5. Test the theme!"
echo ""
echo "🛑 To stop containers:"
echo "  docker compose down"
echo ""
echo "📖 For more info, see: DOCKER-SETUP.md"
echo ""
