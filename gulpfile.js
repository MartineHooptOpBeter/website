
/*******************************************************************************
** SETTINGS                                                                   **
*******************************************************************************/

var defaultDevelopmentServerHostName = '127.0.0.1';
var defaultdevelopmentServerHostIsSecure = false; 
var defaultMinifyCss = false;
var defaultMinifyJs = false;


/*******************************************************************************
** DEPENDENCIES                                                               **
*******************************************************************************/

var gulp = require('gulp');
var lodash = require('lodash');

var ejs = require('ejs');
var dateformat = require('dateformat');

var browsersync = require('browser-sync').create();

var util = require('gulp-util');
var sass = require('gulp-sass')
var watch = require('gulp-watch');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var replace = require('gulp-replace');
var gulpejs = require('gulp-ejs');
var gettext = require('gulp-gettext');
var plumber = require('gulp-plumber');
var iconfont = require('gulp-iconfont');
var cleancss = require('gulp-clean-css');
var consolidate = require('gulp-consolidate');
var autoprefixer = require('gulp-autoprefixer');


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

// Is development server secure?
var developmentServerHostIsSecure = defaultdevelopmentServerHostIsSecure || isEnabled(util.env.devhostsecure);

// Development Server
var developmentServerHostName = '';
if (!(developmentServerHostName = (util.env.devhostname ? util.env.devhostname : defaultDevelopmentServerHostName))) {
	console.log('ERROR: Specify development hostname using: --devhostname=<hostname>');
	process.exit(1);
}
var developmentServerHostURL = 'http' + (developmentServerHostIsSecure ? 's' : '') + '://' + developmentServerHostName + '/';

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
var srcCssFontDir = srcCssDir + 'fonts/';

var dstDir = 'wwwroot/';
var themeDir = dstDir + 'wp-content/themes/martinehooptopbeter/';
var themeCssDir = themeDir + 'css/';
var themeImgDir = themeDir + 'img/';
var themeFontDir = themeDir + 'fonts/';

var vendorsDir = 'vendor/';

var sponsorsSrcDir = srcDir + 'sponsors/';
var sponsorsDstDir = themeDir + 'sponsors/';

var files = {
	
	/* PHP files */
	php_files_src : [srcDir + '*.php'],
	php_files_dest : themeDir,
	
	/* Vendor folders */
	vendors_src : [vendorsDir + 'mollie/mollie-api-php/src/**/*'],
	vendors_dest : themeDir,

	/* Locale files */
	localization_src : [srcDir + 'languages/*.po'],
	localization_dest : themeDir + 'languages/',

	/* Sponsors files */
	sponsors_src: [sponsorsSrcDir + '*.svg', sponsorsSrcDir + '*.png'],
	sponsors_dest : sponsorsDstDir,

	/* Root images */
	root_img_src : [srcDir + 'design/theme/*.png', srcDir + 'design/theme/*.ico'],
	root_img_dest : themeDir,

	/* Copy Images */
	copy_img_src : [srcDir + 'design/logo/**/*.svg', srcDir + 'design/photos/*.jpg', srcDir + 'img/*.*', srcDir + 'design/betaalmethodes/*.svg'],
	copy_img_dest : themeImgDir,

	/* Style CSS file */
	style_css_src : [srcCssDir + 'style.css'],
	style_css_dest : themeDir,
	
	/* Style CSS file */
	allpages_css_src : [srcCssDir + 'reset.scss', srcCssFontDir + '*.css', srcCssDir + 'main.scss'],
	allpages_css_dep : [],
	allpages_css_out : 'allpages.css',
	allpages_css_dest : themeCssDir,

	/* Icon font */
	font_icon_src : srcDir + 'fonts/icons/*.svg',
	font_icon_dest : 'icons',
	font_icon_tpl : srcDir + 'fonts/icons/_template.css',
	font_icon_class : 'icon',

	/* General for all fonts */
	font_dest : themeFontDir,
	font_css_dest : srcCssFontDir,
	
	/* License */
	license_src : srcDir + 'LICENSE',

}

/* Set release date to now */
var release_date = new Date();
var release_year = dateformat(release_date, 'yyyy');
var copyright_firstyear = '2016';

/* Read package file */
var package = require('./package.json');
var release = {
	version : package.version,
	description : package.description,
	date : dateformat(release_date, 'dddd, d mmmm yyyy, HH:MM:ss'),
	author : package.author,
	copyright : 'Copyright (c) ' + copyright_firstyear + (copyright_firstyear != release_year ? ('-' + release_year) : '') + ' Stichting Martine Hoopt Op Beter',
	homepage : package.homepage,
	license : 'MIT License (https://github.com/MartineHooptOpBeter/website/LICENSE)'
};

/* MIT License */
var license_text = ['MIT License',
  '<%= release.copyright %>','',
  'Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:','',
  'The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.','',
  'THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.'
].join('\n');

/* Header tex to be include in every PHP file */
var php_header_text = ['/**',
  ' * <%= release.description %>',
  ' *',
  ' * @version   v<%= release.version %>',
  ' * @released  <%= release.date %>',
  ' *',
  ' * @author    <%= release.author %>',
  ' * @copyright <%= release.copyright %>',
  ' * @license   <%= release.license %>',
  ' * @link      <%= release.homepage %>',
  ' */'].join('\n    ');

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
		.pipe(replace('@@HEADER@@', php_header_text))
		.pipe(gulpejs({ release : release }))
        .pipe(gulp.dest(files.php_files_dest));
});


/*******************************************************************************
** VENDOR FOLDERS                                                             **
*******************************************************************************/

var vendors = 'vendors';
gulp.task(vendors, function() {
    return gulp.src(files.vendors_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.vendors_dest));
});


/*******************************************************************************
** LOCALIZATION TASKS                                                         **
*******************************************************************************/

var localization = 'localization';
gulp.task(localization, function() {
    return gulp.src(files.localization_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(gettext())
        .pipe(gulp.dest(files.localization_dest));
});


/*******************************************************************************
** SPONSOR TASK                                                               **
*******************************************************************************/

var sponsors = 'sponsors';
gulp.task(sponsors, function() {
    return gulp.src(files.sponsors_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.sponsors_dest));
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
** FONT TASKS                                                                 **
*******************************************************************************/

var font_icon = 'font-icon';
gulp.task(font_icon, function() {
    return gulp.src(files.font_icon_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(iconfont({
			fontName: files.font_icon_dest,
			fontHeight: 1000,
			formats: ['ttf', 'eot', 'woff', 'woff2'],
			normalize: true,
			appendCodepoints: true
		}))
		.on('glyphs', function(glyphs, options) {
			gulp.src(files.font_icon_tpl)
				.pipe(consolidate('lodash', {
					glyphs: glyphs,
					fontName: options.fontName,
					fontPath: '../fonts/',
					className: files.font_icon_class,
					packageVersion: package.version
				}))
				.pipe(rename({ basename: files.font_icon_dest }))
				.pipe(gulp.dest(files.font_css_dest));
		})
		.pipe(gulp.dest(files.font_dest));
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
gulp.task(allpages_css, [font_icon], function() {
    return gulp.src(files.allpages_css_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(sass())
		.pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: false }))
        .pipe(gulpif(minifyCss, cleancss({ rebase: false })))
        .pipe(concat(files.allpages_css_out))
        .pipe(gulp.dest(files.allpages_css_dest))
        .pipe(reload({ stream: true }));
});


/*******************************************************************************
** LICENSE FILE                                                               **
*******************************************************************************/

var license = 'license';
gulp.task(license, function() {
    return gulp.src(files.license_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(replace('@@LICENSE@@', license_text))
		.pipe(gulpejs({ release : release }))
        .pipe(gulp.dest('./'))
		.pipe(gulp.dest(themeDir));
});


/*******************************************************************************
** GULP TASKS                                                                 **
*******************************************************************************/

// Set up default task dependencies (i.e. the tasks we want to run by default)
var defaultTaskDependencies = [license, php_files, vendors, localization, sponsors, root_img, copy_img, style_css, font_icon, allpages_css];

// Run default task
gulp.task('default', defaultTaskDependencies, function() {

	/* Start watch tasks for development environment only */
	if (isDevelopmentBuild) {
		browsersync.init({ proxy: developmentServerHostURL, secure: developmentServerHostIsSecure });
		gulp.watch(files.php_files_src, [php_files]).on('change', browsersync.reload);
		gulp.watch(files.localization_src, [localization]).on('change', browsersync.reload);
		gulp.watch(files.sponsors_src, [sponsors]);
		gulp.watch(files.copy_img_src, [copy_img]).on('change', browsersync.reload);
		gulp.watch(files.allpages_css_dep, [allpages_css]);
	}
	
});
