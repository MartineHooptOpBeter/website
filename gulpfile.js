
/*******************************************************************************
** SETTINGS                                                                   **
*******************************************************************************/

var defaultDevelopmentServerHostName = '127.0.0.1';
var defaultMinifyCss = false;
var defaultMinifyJs = false;


/*******************************************************************************
** DEPENDENCIES                                                               **
*******************************************************************************/

var gulp = require('gulp');

var browsersync = require('browser-sync').create();

var util = require('gulp-util');
var sass = require('gulp-sass')
var watch = require('gulp-watch');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var plumber = require('gulp-plumber');
var cleancss = require('gulp-clean-css');


/*******************************************************************************
** FUNCTIONS                                                                  **
*******************************************************************************/

function isEnabled(s) {
	return (s && (s.length > 0) && ((s.toUpperCase() === 'TRUE') || (s.toUpperCase() === 'YES') || (s === '1')))
}


/*******************************************************************************
** GLOBALS                                                                    **
*******************************************************************************/

// Production build
var isProductionBuild = util.env.build === 'production';
var isDevelopmentBuild = !isProductionBuild

// Development Server
var developmentServerHostName = '';
if (!(developmentServerHostName = (util.env.devhostname ? util.env.devhostname : defaultDevelopmentServerHostName))) {
	console.log('ERROR: Specify development hostname using: --devhostname=<hostname>');
	process.exit(1);
}
var developmentServerHostURL = 'http://' + developmentServerHostName + '/';

// Minify CSS
var minifyCss = isEnabled(util.env.minifycss) || defaultMinifyCss || isProductionBuild;

// Minify Javascript
var minifyJs = isEnabled(util.env.minifyjs) || defaultMinifyJs || isProductionBuild;

var reload = browsersync.reload;


/*******************************************************************************
** FILE NAMES                                                                 **
*******************************************************************************/

var srcDir = 'src/';
var srcCssDir = srcDir + 'css/';

var dstDir = 'wwwroot/';
var themeDir = dstDir + 'wp-content/themes/martinehooptopbeter/';
var themeCssDir = themeDir + 'css/';
var themeImgDir = themeDir + 'img/';

var files = {
	
	/* PHP files */
	php_files_src : [srcDir + '*.php'],
	php_files_dest : themeDir,

	/* Root images */
	root_img_src : [srcDir + 'design/theme/screenshot.png'],
	root_img_dest : themeDir,

	/* Copy Images */
	copy_img_src : [srcDir + 'design/logo/martine-hoopt-op-beter.svg', srcDir + 'design/photos/*.jpg', srcDir + 'img/*.*'],
	copy_img_dest : themeImgDir,

	/* Style CSS file */
	style_css_src : [srcCssDir + 'style.css'],
	style_css_dest : themeDir,
	
	/* Style CSS file */
	allpages_css_src : [srcCssDir + 'reset.scss', srcCssDir + 'main.scss'],
	allpages_css_dep : [],
	allpages_css_out : 'allpages.css',
	allpages_css_dest : themeCssDir,

}

/* Fix up properties in files object */
files.allpages_css_dep = files.allpages_css_src
files.allpages_css_dep.push(srcCssDir + 'colors.scss');


/*******************************************************************************
** PHP TASKS                                                                  **
*******************************************************************************/

var php_files = 'php_files';
gulp.task(php_files, function() {
    return gulp.src(files.php_files_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.php_files_dest));
});


/*******************************************************************************
** IMAGE TASKS                                                                **
*******************************************************************************/

var root_img = 'root_img';
gulp.task(root_img, function() {
    return gulp.src(files.root_img_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.root_img_dest));
});

var copy_img = 'copy_img';
gulp.task(copy_img, function() {
    return gulp.src(files.copy_img_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.copy_img_dest));
});


/*******************************************************************************
** CSS TASKS                                                                  **
*******************************************************************************/

var style_css = 'style_css';
gulp.task(style_css, function() {
    return gulp.src(files.style_css_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.style_css_dest));
});

var allpages_css = 'allpages_css';
gulp.task(allpages_css, function() {
    return gulp.src(files.allpages_css_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(sass())
        .pipe(gulpif(minifyCss, cleancss()))
        .pipe(concat(files.allpages_css_out))
        .pipe(gulp.dest(files.allpages_css_dest))
        .pipe(reload({ stream: true }));
});


/*******************************************************************************
** GULP TASKS                                                                 **
*******************************************************************************/

// Set up default task dependencies (i.e. the tasks we want to run by default)
var defaultTaskDependencies = [php_files, root_img, copy_img, style_css, allpages_css];

// Run default task
gulp.task('default', defaultTaskDependencies, function() {

	/* Start watch tasks for development environment only */
	if (isDevelopmentBuild) {
		browsersync.init({ proxy: developmentServerHostName });
		gulp.watch(files.php_files_src, [php_files]).on('change', browsersync.reload);
		gulp.watch(files.copy_img_src, [copy_img]).on('change', browsersync.reload);
		gulp.watch(files.allpages_css_dep, [allpages_css]);
	}
	
});
