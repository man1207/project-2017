/* Пока без продакшна */

var gulp = require('gulp'),
	less = require('gulp-less'),
	browserSync = require('browser-sync'),
	concat = require('gulp-concat'),
	rename = require('gulp-rename');

gulp.task('less', function() { //работа препроцессора
	return gulp.src([
		'src/less/*.less',
		'src/less/global/*.less',
		'src/blocks/**/*.less'
		])
		.pipe(rename({dirname: ''}))
		.pipe(concat('style.less'))
		.pipe(less())
		.pipe(gulp.dest('src/css'))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('img', function(){
	//return gulp.src([
	//	'src/blocks/**/*.png',
	//	'src/blocks/**/*.jpg'
	//	])
	//	.pipe(rename({dirname: ''}))
	//	.pipe(gulp.dest('src/img'));
	
});

gulp.task('browser-sync', function() {//запуск сервера
	browserSync({
		server: {
			baseDir: 'src'
		},
		notify: false
	});
	
});

gulp.task('watch', ['browser-sync', 'img', 'less'], function() { //наблюдение за изменениями в коде
	gulp.watch([
		'src/less/*.less',
		'src/less/global/*.less',
		'src/blocks/**/*.less'
		], ['less']);	
	gulp.watch('src/*.html', browserSync.reload);
	gulp.watch([
		'src/blocks/**/*.jpg',
		'src/blocks/**/*.png'
		], ['img', browserSync.reload]);
});