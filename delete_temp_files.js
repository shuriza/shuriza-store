const fs = require('fs');
const path = require('path');

const files = ['_replace_categories.py','_temp_write_create.js'];

files.forEach(f => {
  try {
    const filePath = path.join(__dirname, f);
    fs.unlinkSync(filePath);
    console.log('deleted', f);
  } catch(e) {
    console.log('skip', f);
  }
});
