const fs = require('fs');
const path = require('path');

// Create the required directories
const dirs = [
  path.join(__dirname, '../../views/pages'),
  path.join(__dirname, '../../views/errors'),
  path.join(__dirname, '../../views/admin/reviews'),
];

dirs.forEach(dirPath => {
  try {
    fs.mkdirSync(dirPath, { recursive: true });
  } catch (err) {
    // Ignore
  }
});

module.exports = {};
