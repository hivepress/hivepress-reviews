const fs = require('fs');
const path = require('path');

const assets = [
	'raty-js/lib/jquery.raty.js',
];

assets.forEach((asset) => {
	const src = path.join('node_modules', asset);
	const dest = path.join('assets/vendor', asset);
	const isDir = fs.statSync(src).isDirectory();
	const destDir = isDir ? dest : path.dirname(dest);

	if (!fs.existsSync(destDir)) {
		fs.mkdirSync(destDir, { recursive: true });
	}

	if (isDir) {
		fs.cpSync(src, dest, { recursive: true });
	} else {
		fs.copyFileSync(src, dest);
	}
});
