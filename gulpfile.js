/* Пока без продакшна */

var gulp = require('gulp'),
	less = require('gulp-less'),
	browserSync = require('browser-sync'),
	concat = require('gulp-concat'),
	rename = require('gulp-rename'),
	shell = require('gulp-shell');

gulp.task('less', function() { //работа препроцессора
	return gulp.src([
		'src/less/global/*.less',
		'src/less/*.less',
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


gulp.task('bem',  function() {
	//console.log(process.argv);
	
	var dir = "src/blocks";
	
	if (process.argv.length > 4) {
		//console.log(process.argv[2]);
		//1. Создаем блок
		//bem -b block-name
		if (process.argv[3] == '-b') {
			
			var block_name = process.argv[4];
			if (block_name.indexOf('__') > -1 || block_name.indexOf('--') > -1) {
				console.log(' Syntax error : Can\'t create bem-block.');
				return;
			}
			console.log(block_name);
			
			
		//2. Создаем элемент
		//bem -e block-name__element-name
		} else if (process.argv[3] == '-e') {
			if (process.argv[4].indexOf('__') == -1 || process.argv[4].indexOf('--') > -1) {
				console.log(' Syntax error : Can\'t create bem-element.');
				return;
			}
			var block_name = process.argv[4].substring(0, process.argv[4].indexOf('__'));
			var element_name = process.argv[4].substring(process.argv[4].indexOf('__') + 2, process.argv[4].length - process.argv[4].indexOf('__') + 5);
			console.log(block_name);
			console.log(element_name);
		//2. Создаем модификатор блока
		//bem -m block-name--modifier
		} else if (process.argv[3] == '-m') {
			if (process.argv[4].indexOf('--') == -1 || process.argv[4].indexOf('__') > -1) {
				console.log(' Syntax error : Can\'t create bem-modifier for block.');
				return;
			}
			var block_name = process.argv[4].substring(0, process.argv[4].indexOf('--'));
			var modifier_name = process.argv[4].substring(process.argv[4].indexOf('--') + 2);
			console.log(block_name);
			console.log(modifier_name);
		} else if (process.argv[3] == '-M') {
			if (process.argv[4].indexOf('--') == -1 || process.argv[4].indexOf('__') == -1) {
				console.log(' Syntax error : Can\'t create bem-modifier for element.');
				return;
			}
			var block_name = process.argv[4].substring(0, process.argv[4].indexOf('__'));
			var element_name = process.argv[4].substring(process.argv[4].indexOf('__') + 2, process.argv[4].indexOf('--'));
			var modifier_name = process.argv[4].substring(process.argv[4].indexOf('--') + 2);
			console.log(block_name);
			console.log(element_name);
			console.log(modifier_name);
		}
	}
});