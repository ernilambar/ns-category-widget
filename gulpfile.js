// Env.
require('dotenv').config();

// Config.
var rootPath = './';

// Gulp.
var gulp = require('gulp');

// Browser sync.
var browserSync = require('browser-sync').create();

// Watch.
gulp.task( 'watch', function() {
    browserSync.init({
        proxy: process.env.DEV_SERVER_URL,
        open: true
    });

    // Watch JS files.
    gulp.watch(rootPath + '**/**/*.js').on('change',browserSync.reload);

    // Watch CSS files.
    gulp.watch(rootPath + '**/**/*.css').on('change',browserSync.reload);

    // Watch PHP files.
    gulp.watch(rootPath + '**/**/*.php').on('change',browserSync.reload);
});

// Tasks.
gulp.task( 'default', gulp.series('watch'));
