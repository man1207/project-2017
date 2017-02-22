/* Пока без продакшна */

var gulp = require('gulp')
	,less = require('gulp-less')
	,browserSync = require('browser-sync')
	,concat = require('gulp-concat')
	,rename = require('gulp-rename')
	//,exec = require('gulp-exec')
	,fs = require('fs')
	;

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
			
			var bemDir = 'src/blocks/'+block_name;
			
			if (fs.existsSync(bemDir)) {
				console.log('Run error: block '+block_name+' exists');	
				return;
			}  
			
			fs.mkdirSync(bemDir);
			fs.writeFileSync(bemDir+'/'+block_name+'.less', '.'+block_name+' {\n\t\n}');
			
			return;
		//2. Создаем элемент
		//bem -e block-name__element-name
		} else if (process.argv[3] == '-e') {
			if (process.argv[4].indexOf('__') == -1 || process.argv[4].indexOf('--') > -1) {
				console.log(' Syntax error : Can\'t create bem-element.');
				return;
			}
			var block_name = process.argv[4].substring(0, process.argv[4].indexOf('__'));
			var element_name = process.argv[4].substring(process.argv[4].indexOf('__') + 2);
			console.log(block_name);
			console.log(element_name);
			
			var bemDir = 'src/blocks/'+block_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'.less', '.'+block_name+' {\n\t\n}');
				
			} else {
				console.log('Warning: block '+block_name+' exists');	
			}
			
			var bemDir = 'src/blocks/'+block_name+'/__'+element_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'__'+element_name+'.less', '.'+block_name+' {\n\t&__'+element_name+' {\n\n\t}\n}');				
			} else {
				console.log('Warning: element '+element_name+' exists');	
			}
			
			return;
		//3. Создаем модификатор блока
		//bem -m block-name--modifier-name
		} else if (process.argv[3] == '-m') {
			if (process.argv[4].indexOf('--') == -1 || process.argv[4].indexOf('__') > -1) {
				console.log(' Syntax error : Can\'t create bem-modifier for block.');
				return;
			}
			var block_name = process.argv[4].substring(0, process.argv[4].indexOf('--'));
			var modifier_name = process.argv[4].substring(process.argv[4].indexOf('--') + 2);
			console.log(block_name);
			console.log(modifier_name);
			
			var bemDir = 'src/blocks/'+block_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'.less', '.'+block_name+' {\n\t\n}');
			} else {
				console.log('Warning: block '+block_name+' exists');	
			}
			
			var bemDir = 'src/blocks/'+block_name+'/--'+modifier_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'--'+modifier_name+'.less', '.'+block_name+' {\n\t&--'+modifier_name+' {\n\n\t}\n}');
			} else {
				console.log('Warning: modifier '+modifier_name+' exists');	
			}
			
			return;
		//3. Создаем модификатор элемента
		//bem -M block-name__element-name--modifier-name	
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
			
			var bemDir = 'src/blocks/'+block_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'.less', '.'+block_name+' {\n\t\n}');
			} else {
				console.log('Warning: block '+block_name+' exists');	
			}
			
			var bemDir = 'src/blocks/'+block_name+'/__'+element_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'__'+element_name+'.less', '.'+block_name+' {\n\t&__'+element_name+' {\n\n\t}\n}');
			} else {
				console.log('Warning: element '+element_name+' exists');	
			}
			
			var bemDir = 'src/blocks/'+block_name+'/__'+element_name+'/--'+modifier_name;
			
			if ( ! fs.existsSync(bemDir)) {
				fs.mkdirSync(bemDir);
				fs.writeFileSync(bemDir+'/'+block_name+'__'+element_name+'--'+modifier_name+'.less', '.'+block_name+' {\n\t&__'+element_name+' {\n\t\t&--'+modifier_name+' {\n\n\t\t}\n\t}\n}');
			} else {
				console.log('Warning: modifier '+modifier_name+' exists');	
			}
			
			return;
		}
	}
});