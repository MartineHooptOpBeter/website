
/*******************************************************************************
** SETTINGS                                                                   **
*******************************************************************************/

var defaultDevelopmentServerHostName = '127.0.0.1';
var defaultdevelopmentServerHostIsSecure = false; 
var defaultMinifyCss = false;
var defaultMinifyJs = false;
var defaultCreateIconFont = false;


/*******************************************************************************
** DEPENDENCIES                                                               **
*******************************************************************************/

var gulp = require('gulp');
var gulpif = require('gulp-if');
var argument = require('minimist')(process.argv.slice(2));

var browsersync = require('browser-sync').create();

var ejs = require('ejs');
var sass = require('gulp-sass')
var watch = require('gulp-watch');
var concat = require('gulp-concat');
var lodash = require('lodash');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var replace = require('gulp-replace');
var gulpejs = require('gulp-ejs');
var gettext = require('gulp-gettext');
var plumber = require('gulp-plumber');
var iconfont = require('gulp-iconfont');
var cleancss = require('gulp-clean-css');
var dateformat = require('dateformat');
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
var isProductionBuild = argument.build === 'production';
var isDevelopmentBuild = !isProductionBuild

// Is development server secure?
var developmentServerHostIsSecure = defaultdevelopmentServerHostIsSecure || isEnabled(argument.devhostsecure);

// Development Server
var developmentServerHostName = '';
if (!(developmentServerHostName = (argument.devhostname ? argument.devhostname : defaultDevelopmentServerHostName))) {
	console.log('ERROR: Specify development hostname using: --devhostname=<hostname>');
	process.exit(1);
}
var developmentServerHostURL = 'http' + (developmentServerHostIsSecure ? 's' : '') + '://' + developmentServerHostName + '/';

// Minify CSS
var minifyCss = isEnabled(argument.minifycss) || defaultMinifyCss || isProductionBuild;

// Minify Javascript
var minifyJs = isEnabled(argument.minifyjs) || defaultMinifyJs || isProductionBuild;

// Create icon font
var createIconFont = isEnabled(argument.createiconfont) || defaultCreateIconFont || isProductionBuild;


/*******************************************************************************
** FILE NAMES                                                                 **
*******************************************************************************/

var srcDir = 'src/';
var srcJsDir = srcDir + 'js/';
var srcCssDir = srcDir + 'css/';
var srcCssFontDir = srcCssDir + 'fonts/';

var vendorsDir = 'vendor/';

var dstDir = 'wwwroot/';
var themeDir = dstDir + 'wp-content/themes/martinehooptopbeter/';
var themeJsDir = themeDir + 'js/';
var themeCssDir = themeDir + 'css/';
var themeImgDir = themeDir + 'img/';
var themeFontDir = themeDir + 'fonts/';
var themeVendorDir = themeDir + vendorsDir;

var sponsorsSrcDir = srcDir + 'sponsors/';
var sponsorsDstDir = themeDir + 'sponsors/';

var files = {
	
	/* PHP files */
	php_files_src : [srcDir + '*.php'],
	php_files_dest : themeDir,
	
	/* Vendor folders */
	vendors_src : [vendorsDir + '/**/*'],
	vendors_dest : themeVendorDir,

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

	/* Javascript files */
	all_js_src : [srcJsDir + 'paymentmethods.js'],
	all_js_dest : themeJsDir,

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
** LICENSE FILE                                                               **
*******************************************************************************/

const task_license = () => {
    return gulp.src(files.license_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(replace('@@LICENSE@@', license_text))
		.pipe(gulpejs({ release : release }))
        .pipe(gulp.dest('./'))
		.pipe(gulp.dest(themeDir));
};


/*******************************************************************************
** PHP FILES                                                                  **
*******************************************************************************/

const task_php_files = () => {
    return gulp.src(files.php_files_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(replace('@@HEADER@@', php_header_text))
		.pipe(gulpejs({ release : release }))
        .pipe(gulp.dest(files.php_files_dest));
};


/*******************************************************************************
** VENDOR FOLDERS                                                             **
*******************************************************************************/

const task_vendors = () => {
    return gulp.src(files.vendors_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.vendors_dest));
};


/*******************************************************************************
** LOCALIZATION                                                               **
*******************************************************************************/

const task_localization = () => {
    return gulp.src(files.localization_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(gettext())
        .pipe(gulp.dest(files.localization_dest));
};


/*******************************************************************************
** SPONSORS                                                                   **
*******************************************************************************/

const task_sponsors = () => {
    return gulp.src(files.sponsors_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.sponsors_dest));
};


/*******************************************************************************
** IMAGES                                                                     **
*******************************************************************************/

const task_root_img = () => {
    return gulp.src(files.root_img_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.root_img_dest));
};

const task_copy_img = () => {
    return gulp.src(files.copy_img_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.copy_img_dest));
};


/*******************************************************************************
** FONTS                                                                      **
*******************************************************************************/

const task_font_icon = (done) => {
	if (!createIconFont)
		done();

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
};


/*******************************************************************************
** CSS                                                                        **
*******************************************************************************/

const task_style_css = () => {
    return gulp.src(files.style_css_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
        .pipe(gulp.dest(files.style_css_dest));
};

const task_allpages_css_function = () => {
    return gulp.src(files.allpages_css_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(sass())
		.pipe(autoprefixer())
        .pipe(gulpif(minifyCss, cleancss({ rebase: false })))
        .pipe(concat(files.allpages_css_out))
        .pipe(gulp.dest(files.allpages_css_dest))
        .pipe(browsersync.stream({ match: files.allpages_css_dep }));
};

const task_allpages_css = gulp.series(task_font_icon, task_allpages_css_function);


/*******************************************************************************
** JAVASCRIPT                                                                 **
*******************************************************************************/

var task_all_js = () => {
    return gulp.src(files.all_js_src)
		.pipe(plumber({ errorHandler: function (err) { console.log(err); this.emit('end'); }}))
		.pipe(gulpif(minifyJs, uglify()))
        .pipe(gulp.dest(files.all_js_dest));
};


/*******************************************************************************
** WATCH TASKS
*******************************************************************************/

const reload_browser =(done) => {
	console.log('Reloading');
	browsersync.reload();
	done()
}

const watch_php_files = () => {
	gulp.watch(files.php_files_src, gulp.series(task_php_files, reload_browser));
}

const watch_css_allpages = () => {
	gulp.watch(files.allpages_css_dep, gulp.series(task_allpages_css));
}

const watch_js_all = () => {
	gulp.watch(files.all_js_src, gulp.series(task_all_js, reload_browser));
}

const watch_localization = () => {
	gulp.watch(files.localization_src, gulp.series(task_localization, reload_browser));
}

const watch_sponsors = () => {
	gulp.watch(files.sponsors_src, gulp.series(task_sponsors, reload_browser));
}

const watch_copy_img = () => {
	gulp.watch(files.copy_img_src, gulp.series(task_copy_img, reload_browser));
}


/*******************************************************************************
** BROWSERSYNC TASK
*******************************************************************************/

const browser_sync = () => {
	browsersync.init({ proxy: developmentServerHostURL });
}


/*******************************************************************************
** GULP TASKS                                                                 **
*******************************************************************************/

// Set up build task
const productionTask = (done) => {
	isProductionBuild = true;
	done();
}

// Set up compile task with all compilation tasks
const compileTask = gulp.series(task_license, task_php_files, task_vendors, task_localization, task_sponsors, task_root_img, task_copy_img, task_style_css, task_allpages_css, task_all_js);

// Set up serve task
const serveTask = gulp.series(compileTask, browser_sync);

// Set up watch task
const watchTask = gulp.parallel(watch_php_files, watch_css_allpages, watch_js_all,  watch_localization, watch_sponsors, watch_copy_img);

// Set up build task
gulp.task('build', gulp.series(productionTask, compileTask));

// Run default task
gulp.task('default', gulp.series(serveTask, watchTask));
