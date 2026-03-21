const fs = require('fs');

const dirs = [
  'c:\\Users\\surya\\Desktop\\shuriza-store\\resources\\views\\pages',
  'c:\\Users\\surya\\Desktop\\shuriza-store\\resources\\views\\errors',
  'c:\\Users\\surya\\Desktop\\shuriza-store\\resources\\views\\admin\\reviews',
  'c:\\Users\\surya\\Desktop\\shuriza-store\\resources\\views\\admin\\coupons',
];

dirs.forEach(dirPath => {
  try {
    fs.mkdirSync(dirPath, { recursive: true });
    console.log('✓ Created:', dirPath);
  } catch (err) {
    console.error('✗ Error:', dirPath, err.message);
  }
});
