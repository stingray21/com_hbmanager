/* --------------------------------------------------------
 * Gulp for Joomla development
 * -------------------------------------------------------- */

// Load Gulp
// const gulp         = require('gulp-sass');
const { src, series, parallel, dest, watch } = require('gulp');

// General ------------------------------------------------
const del          = require('del');
const { pipeline } = require('readable-stream');
const sourcemaps   = require('gulp-sourcemaps');
const mode         = require('gulp-mode')({
	modes: ["production", "development"],
	default: "development",
	verbose: false
});
const newer        = require("gulp-newer");
const rename       = require("gulp-rename");
const replace      = require("gulp-replace");
const fs           = require("fs");
const zip          = require("gulp-zip");


// .env ---------------------------------------------------
// load environment variables
// process.env has keys and values as defined in .env file
const dotenv       = require('dotenv');
const result       = dotenv.config();
if (result.error) {
	throw result.error;
}


// Logging ------------------------------------------------
const log          = require('fancy-log');
// prefixes the output with the current time in HH:MM:ss format
//   log(msg...) | log.error(msg...) | log.warn(msg...)
//   log.info(msg...) | log.dir(msg...)
const c            = require('ansi-colors');
// e.g. (from https://www.npmjs.com/package/ansi-colors)
//   console.log(c.bold.red('bold red message'));
//   console.log(c.bold.yellow.italic('bold yellow italicized message'));
//   console.log(c.green.bold.underline('bold green underlined message'));


// CSS ----------------------------------------------------
const sass         = require('gulp-sass')(require('sass'));
const postcss      = require('gulp-postcss');
// processors
const cssnano      = require('cssnano');
const autoprefixer = require('autoprefixer');

// JS -----------------------------------------------------
const babel        = require('gulp-babel');
const terser       = require('gulp-terser');

// Images -------------------------------------------------
const imagemin     = require('gulp-imagemin');


// Versioning ---------------------------------------------
const bump         = require('gulp-bump');
const jeditor      = require("gulp-json-editor");
const dateFormat   = require("dateformat");

// BrowserSync --------------------------------------------
const browserSync  = require('browser-sync').create();
function reload(done) {
	browserSync.reload();
	done();
}
function serve(done) {
	browserSync.init({
		open: false,
		injectChanges: true,
		proxy: process.env.PROXY
	});
	done();
}

/*
 * Variables
 */

// Project related variables
const prefix_pattern    = /^(com|plg|lib|mod|tpl|lan)_(.*)$/;
const localJoomla       = process.env.LOCAL_JOOMLA;
const remoteJoomla      = process.env.FTP_DEST;
const extName           = process.env.EXT_NAME;
const extNameLite       = extName.replace( prefix_pattern, "$2" );
const extType           = extName.replace( prefix_pattern, "$1" );

// Joomla paths
const extDir_lang_site  = "language/";
const extDir_lang_admin = "administrator/language/";
const extDir_com_admin  = "administrator/components/";
const extDir_com_site   = "components/";
let extDir_admin        = "";
let extDir_site         = "";
switch (extType) {
	case 'com':
		extDir_admin    = extDir_com_admin;
		extDir_site     = extDir_com_site;
		break;
	default:
		break;
}
const extDir_media      = "media/";
// Project paths
const buildDir          = "./Source/" + extName + "/";
const srcDir            = "./Source/assets/";
const releaseDir        = "./Releases/";

const paths = {
	styles: {
		watch:  srcDir   + "scss/**/*.scss",
		src:    srcDir   + "scss/**/*.scss",
		dest:   buildDir + "media/css"
	},
	scripts: {
		watch:  srcDir   + "js/**/*.js",
		src:    srcDir   + "js/**/*.js",
		dest:   buildDir + "media/js"
	},
	images: {
		watch:  srcDir   + "img/**/*",
		src:    srcDir   + "img/**/*.{gif,png,jpg,jpeg,svg}",
		dest:   buildDir + "media/images"
	},
	localJoomla: {
		site: {
			src:   [ buildDir + "site/**/*", "!" + buildDir + "site/{language,language/**/*}" ],
			dest:  localJoomla + extDir_site + extName
		},
		sitelanguage: {
			src:   buildDir + "site/language/**/*",
			dest:  localJoomla + extDir_lang_site
		},
		media: {
			src:   buildDir + "media/**/*",
			dest:  localJoomla + extDir_media + extName
		},
		manifest: {
			src:   buildDir + extNameLite + ".xml",
			dest:  localJoomla + extDir_admin + extName
		},
		admin: {
			src:   [ buildDir + "admin/**/*", "!" + buildDir + "admin/{language,language/**/*}" ],
			dest:  localJoomla + extDir_admin + extName
		},
		adminlanguage: {
			src:   buildDir + "admin/language/**/*",
			dest:  localJoomla + extDir_lang_admin
		}
	}
};

/*
 * Tasks 
 */

async function clean() {
	const deletedFilePaths = await del([
		paths.styles.dest  + '/*',
		paths.scripts.dest + '/*',
		// paths.images.dest + '/*',
	]);
	console.log('');
	log.warn(c.bold.cyan('Deleted files:'));
	console.log(c.magenta.strikethrough(deletedFilePaths.join('\n')));
	console.log('');
}

async function cleanLocalJoomla() {
	path = [
		localJoomla + extDir_site + extName + '/**/*',
		localJoomla + extDir_admin + extName + '/**/*',
		localJoomla + extDir_media + extName + '/**/*',
		localJoomla + extDir_lang_admin + '/**/*' + extName + '*.ini',
		localJoomla + extDir_lang_site + '/**/*' + extName + '*.ini',
	];
	const deletedFilePaths = await del(
		path,
		{dryRun: false, force: true}
	);
	log.warn(c.bold.cyan('Deleted files:'));
	log.warn(c.magenta(localJoomla));
	const deletedFiles = ' - ' + deletedFilePaths.map( p => p = p.replace(localJoomla,'')).join('\n - ');
	console.log(c.magenta.strikethrough(deletedFiles));
	console.log('');
}

function styles() {
	return pipeline(
		src(paths.styles.src),
		mode.development(sourcemaps.init()),
		sass().on('error', sass.logError),
		mode.development(postcss([autoprefixer])),
		mode.production(postcss([autoprefixer, cssnano])),
		mode.development(sourcemaps.write('.')),
		dest(paths.styles.dest)
	);
}

function scripts() {
	return pipeline(
		src(paths.scripts.src),
		babel({ presets: ['@babel/env'] }),
		mode.development(sourcemaps.init()),
		mode.production( terser({ output: {comments: false}})),
		mode.production(rename({ basename: "site", suffix: ".min"})),
		mode.development(sourcemaps.write('.')),
		dest(paths.scripts.dest)
	);
}

function images() {
	return pipeline(
		src(paths.images.src),
		imagemin([
			imagemin.gifsicle({}),
			imagemin.mozjpeg({}),
			imagemin.optipng({}),
			imagemin.svgo({
				plugins: [
					{removeViewBox: true},
					{cleanupIDs: false}
				]
			})
		]),
		dest(paths.images.dest)
	);
}


// Copy to local Joomla site ------------------------------
function logFiles(file, destDir) {
	let fileName = ('.' + file.path.replace(__dirname, '')).replace(buildDir, '');
	if (destDir.includes("/language/")) {
		destDir = getLanguagePath(file, destDir);
	}
	console.log(c.magenta('  ⇒ ' + destDir + file.basename));
	return destDir;
}
function copyToLocalJoomla(cb) {
	Object.entries(paths.localJoomla).forEach(entry => {
		const [key, path] = entry;
		let srcPath = path.src;
		let newerPath = path.dest;
		let destPath = path.dest;
		if (srcPath.includes("/language/")) {
			newerPath = {dest: path.dest, map: getLanguageNewerPath };
		}
		log('Copy ' + c.magenta(key.padEnd(15, ' ')) + ' to ' + c.magenta(path.dest));
		pipeline( 
			src(srcPath), 
			newer(newerPath), 
			dest(file => logFiles(file, destPath))
		);
	});
	cb();
}

const languagePattern = /^([a-z]{2}-[A-Z]{2})\.(.*)(\.sys)?(\.ini)$/;
function getLanguagePath(file, dest = null) {
	// log(file.path, file.basename, dest);
	let langDir = file.relative.replace(languagePattern, "$1/");
	return dest + langDir;
}
function getLanguageNewerPath(file) {
	// log(file.path, file.basename, dest);
	let langDir = file.replace(languagePattern, "$1/");
	return langDir + file;
}

// Versioning ---------------------------------------------
function bumpVersion(type) {
	// usage: e.g. bump --minor
	let defaultType = 'patch';
	let types = ['major', 'minor', 'patch', 'prerelease'];
	type = (3 in process.argv) ? process.argv[3].replace('--', '').toLowerCase() : type;
	type = (type === undefined) ? defaultType : type;
	type = (types.indexOf(type) === -1) ? defaultType : type;

	log('Bump: ' + c.magenta(type));
	return pipeline(
		src('./package.json'),
		bump({ type: type }),
		dest('./')
	);
}
function updateDate() {
	var now = new Date();
	var newDate = dateFormat(now, 'yyyy-mm-dd HH:MM:ss Z');

	log('Update date in package.json: ' + c.magenta(newDate));
	return pipeline(
		src('./package.json'),
		jeditor({
			date: newDate
		}),
		dest('./')
	);
}
function updateManifest() {
	var package = JSON.parse(fs.readFileSync("./package.json"));
	var manifest = buildDir + extNameLite + ".xml";

	log("Updating manifest file with version and date:");
	log("  file:    " + c.magenta(manifest));
	log("  version: " + c.magenta(package.version));
	log("  date:    " + c.magenta(package.date));

	return pipeline(
		src(manifest),
		replace(
			/<creationDate>(.+?)<\/creationDate>/g,
			"<creationDate>" + package.date + "</creationDate>"
		),
		replace(
			/<version>(.+?)<\/version>/g,
			"<version>" + package.version + "</version>"
		),
		dest(buildDir)
	);
}


function zipRelease() {
	var package = JSON.parse(fs.readFileSync("./package.json"));
	var now = new Date(package.date);
	var date = dateFormat(now, "yyyymmdd-HHMMss");

	var fileName = extName + "_" + date + "_" + package.version + ".zip";
	log("Package Release File: " + c.magenta(fileName));
	return pipeline(
		src(buildDir + "**/*"),
		zip(fileName),
		dest(releaseDir)
	);
}

/*
 * Task declaration
 */

function watchStyles() {
	return watch(
		paths.styles.watch,
		series(styles, reload)
	);
}
function watchScripts() {
	return watch(
		paths.scripts.watch,
		series(scripts, reload)
	);
}
function watchImages() {
	return watch(
		paths.images.watch,
		series(images, reload)
	);
}
function watchFiles() {
	return watch(
		buildDir,
		series(copyToLocalJoomla, reload)
	);
}

const build        = series(clean, parallel(styles, scripts, images));

const watchTasks   = parallel(watchStyles, watchScripts, watchImages, watchFiles);

const dev          = series(build, copyToLocalJoomla, serve, watchTasks);

const bumpup       = series(bumpVersion, updateDate, updateManifest);

const release      = series(build, bumpup, zipRelease);
const releaseNoBump = series(build, updateDate, updateManifest, zipRelease);

exports.clean      = clean;
exports.styles     = styles;
exports.scripts    = scripts;
exports.images     = images;
exports.serve      = serve;
exports.zip        = zipRelease;
exports.cleanLocal = cleanLocalJoomla;
exports.tolocal    = copyToLocalJoomla;
exports.build      = build;
exports.watch      = watchTasks;
exports.dev        = dev;
exports.bump       = bumpup;
exports.release    = release;
exports.release_nb = releaseNoBump;

/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default    = dev;
