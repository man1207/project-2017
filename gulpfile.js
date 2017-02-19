/* Переписать файл согласно BEM-методологии */
var gulp = require('gulp'),
	less = require('gulp-less'),
	browserSync = require('browser-sync'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglifyjs'),
	cssnano = require('gulp-cssnano'),
	rename = require('gulp-rename'),
	del = require('del');

gulp.task('less', function(){
	return gulp.src('app/less/**/*.less')
		.pipe(less())
		.pipe(gulp.dest('app/css'))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('browser-sync', function(){
	browserSync({
		server: {
			baseDir: 'app'
		},
		notify: false
	});
});

gulp.task('scripts', function() {
	return gulp.src([
		'app/libs/jquery/dist/jquery.min.js',
		'app/libs/magnific-popup/dist/jquery.magnific-popup.min.js'])
		.pipe(concat('libs.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('app/js'));
});

gulp.task('css-libs', function(){
	return gulp.src(['app/libs/magnific-popup/dist/magnific-popup.css'])
		.pipe(cssnano())
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('app/css'));
}); 

gulp.task('watch', ['browser-sync', 'css-libs', 'less', 'scripts'], function(){
	//Интересно как это работает. Проверить!
	gulp.watch('app/less/**/*.less', ['less']);
	gulp.watch('app/*.html', browserSync.reload);
	gulp.watch('app/js/**/*.js', browserSync.reload);
});

gulp.task('clean' , function() {
	return del.sync('dist');
});

gulp.task('build', ['clean', 'less', 'scripts'], function(){
	var buildCss = gulp.src([
		'app/css/main.css',
		'app/css/magnific-popup.min.css'])
	.pipe(gulp.dest('dist/css'));
	
	var buildFonts = gulp.src('app/fonts/**/*')
	.pipe(gulp.dest('dist/fonts'));
	
	
	var buildJs = gulp.src('app/js/**/*')
	.pipe(gulp.dest('dist/js'));
	
	var buildHtml = gulp.src('app/*.html')
	.pipe(gulp.dest('dist'));
});

