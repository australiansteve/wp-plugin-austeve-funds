// Load our plugins
var	gulp			=	require('gulp'),
	argv = 				require('yargs').argv, //Arguments parser
	sass			=	require('gulp-sass'),  // Our sass compiler
	notify			=	require('gulp-notify'), // Basic gulp notificatin using OS
	sourcemaps		= require('gulp-sourcemaps'), // Sass sourcemaps
	size					= require('gulp-size'),
	minifycss		=	require('gulp-minify-css'), // Minification
	rename			=	require('gulp-rename'), // Allows us to rename our css file prior to minifying
	autoprefixer	=	require('gulp-autoprefixer'), // Adds vendor prefixes for us
	concat			= require('gulp-concat'), // Concat our js
	uglify			= require('gulp-uglify'); // Minify our js

var paths = {
	sassPath: 'sass/',
	destPath: '.'
};

//Our 'deploy' task which deploys on a local dev environment
gulp.task('deploy', function() {

	var files = ['images/**.*',
		'*.php',
		'*.css',
		'js/*.js'];

	var destThemeDev = '/Applications/MAMP/local.cfsj/wp-content/plugins/austeve-faqs';

	return gulp.src(files, {base:"."})
    		.pipe(gulp.dest(destThemeDev));
});

gulp.task('styles', function() {
	gulp.src('style.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({
			outputStyle: 'compressed'
		})
		.on('error', notify.onError(function(error) {
			return "Error: " + error.message;
		}))
		)
		.pipe(sourcemaps.write('.'))
		.pipe(size({showFiles: true}))
		.pipe(gulp.dest('.'))
		.pipe(notify({
			message: "✔︎ Styles task complete",
			onLast: true
		}));
});
// Our default gulp task, which runs all of our tasks upon typing in 'gulp' in Terminal
gulp.task('default', ['styles', 'deploy']);
