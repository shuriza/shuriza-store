#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

const baseDir = process.cwd();
const dirs = [
  path.join(baseDir, 'resources/views/pages'),
  path.join(baseDir, 'resources/views/errors'),
  path.join(baseDir, 'resources/views/admin/reviews'),
  path.join(baseDir, 'resources/views/admin/coupons'),
  path.join(baseDir, 'resources/views/emails'),
  path.join(baseDir, 'app/Mail'),
];

console.log('Creating directories...');
dirs.forEach(dirPath => {
  try {
    fs.mkdirSync(dirPath, { recursive: true });
    console.log('✓ Created:', dirPath);
  } catch (err) {
    console.error('✗ Error:', dirPath, err.message);
  }
});

console.log('Directory creation complete!');
