const fs = require('fs');
const path = require('path');

const cssDir = path.join(__dirname, 'public', 'build', 'assets');

if (!fs.existsSync(cssDir)) {
    console.log('No CSS directory found.');
    process.exit(0);
}

const files = fs.readdirSync(cssDir).filter(f => f.endsWith('.css'));

for (const file of files) {
    const filePath = path.join(cssDir, file);
    let content = fs.readFileSync(filePath, 'utf8');

    // Remove text-size-adjust and -webkit-text-size-adjust
    content = content.replace(/text-size-adjust:[^;]+;/g, '');
    content = content.replace(/-webkit-text-size-adjust:[^;]+;/g, '');

    // Remove scrollbar-color and scrollbar-width
    content = content.replace(/scrollbar-color:[^;]+;/g, '');
    content = content.replace(/scrollbar-width:[^;]+;/g, '');

    // Remove @starting-style blocks (naive regex, assuming they look like @starting-style{...})
    content = content.replace(/@starting-style\s*{[^}]*}/g, '');

    fs.writeFileSync(filePath, content);
    console.log(`Stripped CSS warnings from ${file}`);
}
