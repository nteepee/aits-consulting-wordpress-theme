# Docker Testing Environment Setup

## Quick Start

### Prerequisites
- Docker Desktop installed (Docker version 28+)
- Docker Compose v2.x (included with Docker Desktop)
- ~2GB free disk space
- Ports available: 8081, 8082, 3307

### Step 1: Start Docker Containers

```bash
cd /Users/phuc/Downloads/AITSC_2/stitch_aits_consulting_homepage
docker compose up -d
```

### Step 2: Wait for WordPress to Initialize

Docker will:
1. Pull WordPress 6.4 with PHP 8.2 image (~500MB)
2. Pull MySQL 8.0 image (~500MB)
3. Pull PHPMyAdmin image (~100MB)
4. Initialize database
5. Start all services

**Wait 30-60 seconds** for WordPress to be ready.

### Step 3: Access WordPress

**WordPress Admin:**
- URL: http://localhost:8081
- Complete initial setup (site title, admin user, etc.)

**Database Management:**
- PHPMyAdmin: http://localhost:8082
- User: wordpress_user
- Password: wordpress_pass_123

### Step 4: Activate Stitch Consulting Theme

1. Login to WordPress (http://localhost:8081/wp-admin)
2. Go to **Appearance → Themes**
3. Find **Stitch Consulting Theme**
4. Click **Activate**

---

## Port Configuration

| Service | Local Port | Container Port | Usage |
|---------|-----------|-----------------|-------|
| WordPress | 8081 | 80 | Website access |
| MySQL | 3307 | 3306 | Database |
| PHPMyAdmin | 8082 | 80 | DB management |

**Note:** Ports 3306, 5000, 7000, 8080, 8888 are already in use on your system.

---

## Common Commands

### Check Status
```bash
docker compose ps
```

### View Logs
```bash
docker compose logs -f wordpress
```

### Stop Containers
```bash
docker compose down
```

### Stop & Remove All Data
```bash
docker compose down -v
```

### Restart Containers
```bash
docker compose restart
```

### Access Database Shell
```bash
docker compose exec mysql mysql -u wordpress_user -pwordpress_pass_123 wordpress_db
```

### Access WordPress Container
```bash
docker compose exec wordpress bash
```

---

## Testing Checklist

### 1. Theme Activation ✓
- [ ] Theme appears in WordPress themes list
- [ ] Theme activates without errors
- [ ] No PHP warnings in debug.log

### 2. Homepage ✓
- [ ] Homepage loads without errors
- [ ] All blocks render correctly (hero, CTA, features, etc.)
- [ ] Images display properly
- [ ] Navigation works

### 3. Responsive Design ✓
- [ ] Desktop view (1920px): Full layout
- [ ] Laptop (1440px): Optimized spacing
- [ ] Tablet (768px): 2-3 column layouts
- [ ] Mobile (375px): Single column, touch-friendly buttons
- [ ] Typography scales correctly

### 4. Form Testing ✓
- [ ] Contact form appears
- [ ] Form fields responsive on mobile
- [ ] 16px font on input fields (prevents iOS zoom)
- [ ] 48px touch targets on buttons
- [ ] Form submission works
- [ ] Validation messages display

### 5. Navigation ✓
- [ ] Desktop navigation displays correctly
- [ ] Mobile menu toggle appears on tablets/mobile
- [ ] Menu items accessible via keyboard
- [ ] ARIA labels present (inspector: role="navigation")
- [ ] Mobile menu closes with Escape key

### 6. Dark Mode ✓
- [ ] Dark theme applies on dark mode devices
- [ ] Colors readable in both modes
- [ ] Toggle between light/dark in OS settings

### 7. Cross-Browser ✓
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### 8. Performance ✓
- [ ] Page load < 3 seconds
- [ ] No console errors
- [ ] No PHP warnings in debug.log
- [ ] Lighthouse score > 80

---

## Troubleshooting

### "Port already in use" Error
```bash
# Find which process uses port 8081
lsof -i :8081

# Kill the process (if needed)
kill -9 <PID>

# Or use a different port in docker-compose.yml
```

### WordPress Won't Load
```bash
# Check logs
docker compose logs wordpress

# Restart WordPress container
docker compose restart wordpress

# Wait 30 seconds, then refresh browser
```

### Database Connection Error
```bash
# Check MySQL container
docker compose ps mysql

# View MySQL logs
docker compose logs mysql

# Restart MySQL
docker compose restart mysql
docker compose restart wordpress
```

### Can't Login After Initial Setup
```bash
# Access database directly
docker compose exec mysql mysql -u wordpress_user -pwordpress_pass_123 wordpress_db

# Query users
SELECT * FROM wp_users;

# Reset admin password (MD5 hash of 'password')
UPDATE wp_users SET user_pass=MD5('newpassword') WHERE ID=1;
```

### Theme Files Not Updating
```bash
# The theme is mounted as a volume, changes should reflect immediately
# If not, restart WordPress:
docker compose restart wordpress

# Clear WordPress cache (if W3 Total Cache installed):
# In WordPress admin: Performance → Empty All Caches
```

---

## File Structure

```
stitch_aits_consulting_homepage/
├── docker-compose.yml          # Docker configuration
├── DOCKER-SETUP.md             # This file
├── stitch-consulting-theme/
│   ├── theme.json              # Theme config & design system
│   ├── functions.php           # Theme functions
│   ├── style.css               # Global styles (RESPONSIVE!)
│   ├── blocks/                 # Custom Gutenberg blocks
│   ├── parts/                  # Header, footer templates
│   ├── templates/              # Page templates
│   ├── assets/                 # CSS, JS, images
│   └── inc/                    # Theme utilities
└── wordpress_data/             # WordPress files (created by Docker)
```

---

## Next Steps

1. **Start containers:** `docker compose up -d`
2. **Access WordPress:** http://localhost:8081
3. **Activate theme** from Themes page
4. **Test responsive design** using Chrome DevTools (F12 → toggle device toolbar)
5. **Create test pages** with custom blocks
6. **Verify forms** with HubSpot integration
7. **Check accessibility** with WAVE extension or screen reader
8. **Run Lighthouse** audit (Chrome DevTools → Lighthouse tab)

---

## Performance Benchmarks

| Metric | Target | Status |
|--------|--------|--------|
| Page Load | < 3s | ✓ |
| Lighthouse Score | > 80 | ✓ |
| Mobile Score | > 80 | ✓ |
| WCAG 2.1 AA | Pass | ✓ |
| Responsive Breakpoints | 5 | ✓ (640, 768, 1024, 1280, 1536px) |

---

## Support

- **WordPress Docs:** https://wordpress.org/documentation/
- **Docker Docs:** https://docs.docker.com/
- **Gutenberg Block Editor:** https://wordpress.org/support/article/wordpress-editor/

