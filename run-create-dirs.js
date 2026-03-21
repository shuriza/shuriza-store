const fs = require('fs');
const path = require('path');

const baseDir = 'C:\\Users\\surya\\Desktop\\shuriza-store';
const dirs = [
  path.join(baseDir, 'resources\\views\\pages'),
  path.join(baseDir, 'resources\\views\\errors'),
  path.join(baseDir, 'resources\\views\\admin\\reviews'),
];

dirs.forEach(dirPath => {
  try {
    fs.mkdirSync(dirPath, { recursive: true });
    console.log('✓ Created:', dirPath);
  } catch (err) {
    console.error('✗ Error:', dirPath, err.message);
  }
});

console.log('done');
