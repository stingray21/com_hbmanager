// Load Gulp
const gulp = require("gulp");
const newer = require("gulp-newer");
// const dirSync = require("gulp-directory-sync");
const sass = require("gulp-sass");
const babel = require("gulp-babel");
// const concat = require("gulp-concat");
// const uglify = require("gulp-uglify-es").default;
const rename = require("gulp-rename");
// const cleanCSS = require("gulp-clean-css");
const sourcemaps = require("gulp-sourcemaps");
const postcss = require("gulp-postcss");

// Versioning
const bump = require("gulp-bump");
const jeditor = require("gulp-json-editor");
const dateFormat = require("dateformat");
const replace = require("gulp-replace");
// const argv = require("yargs").argv;
const zip = require("gulp-zip");

// Logging
const log = require("fancy-log");
const c = require("ansi-colors");

const del = require("del");
const pipeline = require("readable-stream").pipeline;
const notify = require("gulp-notify");
const fs = require("fs");

// FTP deploy
const gutil = require("gulp-util");
const ftp = require("vinyl-ftp");

// .env
require("dotenv").config();

const extName = process.env.EXT_NAME;
const extNameLite = extName.replace(/^(com|plg|lib|mod|tpl|lan)_(.*)$/, "$2");

// Project related variables
const localJoomla = process.env.LOCAL_JOOMLA;
const remoteJoomla = process.env.FTP_DEST;

// Joomla variables
const extDir_admin = "administrator/components/";
const extDir_site = "components/";
const extDir_media = "media/";

// const localJoomla_dest = localJoomla + extDir + extName + "/";
// const remoteJoomla_dest = remoteJoomla + extDir;
const buildDir = "./" + extName + "/";
const srcDir = "./src/";
const releaseDir = "../Releases/";
const paths = {
	styles: {
		watch: srcDir + "scss/**/*.scss",
		src: srcDir + "scss/admin.scss",
		dest: buildDir + "media/css"
	},
	scripts: {
		watch: srcDir + "js/**/*.js",
		src: srcDir + "js/**/*.js",
		dest: buildDir + "assets/"
	}
};

// Tasks

function clean() {
	return del([buildDir + "assets/"]);
}

function styles() {
	return pipeline(
		gulp.src(paths.styles.src),
		sourcemaps.init(),
		sass(),
		// cleanCSS(),
		postcss([
			// require("tailwindcss"),
			require("precss"),
			require("autoprefixer")
		]),
		// rename({
		// 	basename: "site",
		// 	suffix: ".min"
		// }),
		sourcemaps.write("."),
		gulp.dest(paths.styles.dest)
	);
}

function scripts() {
	return pipeline(
		gulp.src(paths.scripts.src),
		babel(),
		// uglify(),
		rename({
			basename: "site",
			suffix: ".min"
		}),
		gulp.dest(paths.scripts.dest)
	);
}

// function deploy_to_local_Joomla() {
// 	return pipeline(
// 		gulp.src(buildDir + "**/*"),
// 		dirSync(buildDir, localJoomla_dest, { printSummary: true })
// 	);
// }

function copy_to_local_Joomla_admin() {
	var src = buildDir + "admin/**/*";
	var src_exclude = "!(" + buildDir + "admin/language/**/*)";
	var dest = localJoomla + extDir_admin + extName;
	log(src + " --> " + dest);

	return pipeline(gulp.src(src, src_exclude), newer(dest), gulp.dest(dest));
}
function copy_to_local_Joomla_admin_language() {
	var src = buildDir + "admin/language/**/*";
	var dest = localJoomla + 'administrator/language/';
	log(src + " --> " + dest);

	options = {dest: dest, map: getLanguageNewerPath }
	return pipeline(gulp.src(src), newer(options), gulp.dest(file => getLanguagePath(file, dest)));
}

function getLanguageNewerPath(file) {
	// log(file);
	var pattern = /^([a-z]{2}-[A-Z]{2})\.(.*)(\.sys)?(\.ini)$/;
	langDir = file.replace(pattern, "$1/");
	file = langDir + file;
	return file;
}

function getLanguagePath(file, dest) {
	// log(file.path);
	log(c.magenta(file.relative));
	var pattern = /^([a-z]{2}-[A-Z]{2})\.(.*)(\.sys)?(\.ini)$/;
	langDir = file.relative.replace(pattern, "$1/");
	// log(dest + langDir);
	return dest + langDir;
}

function copy_to_local_Joomla_site() {
	var src = buildDir + "site/**/*";
	var src_exclude = "!(" + buildDir + "site/language/**/*)";
	var dest = localJoomla + extDir_site + extName;
	log(src + " --> " + dest);

	return pipeline(gulp.src(src, src_exclude), newer(dest), gulp.dest(dest));
}
function copy_to_local_Joomla_site_language() {
	var src = buildDir + "site/language/**/*";
	var dest = localJoomla + 'language/';
	log(src + " --> " + dest);

	options = {dest: dest, map: getLanguageNewerPath }
	return pipeline(gulp.src(src), newer(options), gulp.dest(file => getLanguagePath(file, dest)));
}

function copy_to_local_Joomla_media() {
	var src = buildDir + "media/**/*";
	var dest = localJoomla + extDir_media + extName;
	log(src + " --> " + dest);

	return pipeline(gulp.src(src), newer(dest), gulp.dest(dest));
}
function copy_to_local_Joomla_manifest() {
	var src = buildDir + extName + ".xml";
	var dest = localJoomla + extDir_admin + extName;
	log(src + " --> " + dest);

	return pipeline(gulp.src(src), newer(dest), gulp.dest(dest));
}

var deploy_to_local_Joomla = gulp.series(
	copy_to_local_Joomla_admin,
	copy_to_local_Joomla_admin_language,
	copy_to_local_Joomla_site,
	copy_to_local_Joomla_site_language,
	copy_to_local_Joomla_media,
	copy_to_local_Joomla_manifest
);

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
var build = gulp.series(clean, gulp.parallel(scripts, styles));
// deploy_local
var deploy_local = gulp.series(build, deploy_to_local_Joomla);

// watch functions
function watch_assets() {
	gulp.watch(paths.scripts.watch, scripts);
	gulp.watch(paths.styles.watch, styles);
}
function watch_deploy_local() {
	gulp.watch(paths.scripts.watch, scripts);
	gulp.watch(paths.styles.watch, styles);
	// gulp.watch(buildDir + "**/*", deploy_to_local_Joomla);
	gulp.watch(buildDir + "admin/**/*", gulp.series(copy_to_local_Joomla_admin, copy_to_local_Joomla_admin_language));
	gulp.watch(buildDir + "site/**/*", gulp.series(copy_to_local_Joomla_site, copy_to_local_Joomla_site_language));
	gulp.watch(buildDir + "media/**/*", copy_to_local_Joomla_media);
	gulp.watch(buildDir + extName + ".xml", copy_to_local_Joomla_manifest);
}

// FTP deploy
function deploy_remote() {
	var conn = ftp.create({
		host: process.env.FTP_HOST,
		user: process.env.FTP_USER,
		password: process.env.FTP_PASSWORD,
		parallel: 10,
		log: gutil.log
	});

	// var globs = [
	//     'src/**',
	//     'css/**',
	//     'js/**',
	//     'fonts/**',
	//     'index.html'
	// ];
	var globs = [buildDir + "**/*"];

	// using base = '.' will transfer everything to /public_html correctly
	// turn off buffering in gulp.src for best performance

	return gulp
		.src(globs, { base: ".", buffer: false })
		.pipe(conn.newer(remoteJoomla_dest)) // only upload newer files
		.pipe(conn.dest(remoteJoomla_dest));
}

/*
 * Versioning
 */
function bump_version(type) {
	// var type = argv.type; // requires yargs | usage: bump --type=minor

	var type = process.argv[3]; // usage: bump --minor
	if (type !== undefined) type = type.replace("--", "");

	var types = ["major", "minor", "patch", "prerelease"];

	if (type === undefined || types.indexOf(type.toLowerCase()) == -1) {
		type = "patch";
	}

	return pipeline(
		gulp.src("./package.json"),
		bump({
			type: type
		}),
		gulp.dest("./")
	);
}

function update_date() {
	var now = new Date();
	var newDate = dateFormat(now, "yyyy-mm-dd HH:MM:ss Z");

	log("Update date in package.json: " + c.magenta(newDate));
	return pipeline(
		gulp.src("./package.json"),
		jeditor({
			date: newDate
		}),
		gulp.dest("./")
	);
}

function update_manifest() {
	// gulp.series(bump_patch, update_date);

	var package = JSON.parse(fs.readFileSync("./package.json"));

	var manifest = buildDir + extNameLite + ".xml";

	log("Updating manifest file with version and date:");
	log("  file:    " + c.magenta(manifest));
	log("  version: " + c.magenta(package.version));
	log("  date:    " + c.magenta(package.date));

	return pipeline(
		gulp.src(manifest),
		replace(
			/<creationDate>(.+?)<\/creationDate>/g,
			"<creationDate>" + package.date + "</creationDate>"
		),
		replace(
			/<version>(.+?)<\/version>/g,
			"<version>" + package.version + "</version>"
		),
		gulp.dest(buildDir)
	);
}

function zip_release() {
	var package = JSON.parse(fs.readFileSync("./package.json"));
	var now = new Date(package.date);
	var date = dateFormat(now, "yyyymmdd-HHMMss");

	var fileName = extName + "_" + date + "_" + package.version + ".zip";
	log("Package Release File: " + c.magenta(fileName));
	return pipeline(
		gulp.src(buildDir + "**/*"),
		zip(fileName),
		gulp.dest(releaseDir)
	);
}

var bumpup = gulp.series(
	bump_version,
	update_date,
	update_manifest,
	zip_release
);

/*
 * You can use CommonJS `exports` module notation to declare tasks
 */
exports.clean = clean;
exports.watch = watch_assets;
exports.build = build;
exports.deploy = deploy_local;
exports.deploy_remote = deploy_remote;
exports.dev = watch_deploy_local;
exports.bump = bumpup;
exports.zip = zip_release;

/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default = build;
