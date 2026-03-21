const fs = require('fs');
const path = require('path');

const dir = path.join(__dirname, 'resources', 'views', 'admin', 'banners');
fs.mkdirSync(dir, { recursive: true });
console.log(`Directory created: ${dir}`);
