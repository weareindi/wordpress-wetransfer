module.exports = (gulp, options, plugins) => {
    gulp.task('scss', gulp.series(
        gulp.parallel('scss-core', 'scss-enhanced')
    ));

    gulp.task('scss-core', () => {
        return gulp.src(process.env.SCSS_SRC + 'core.scss')
            .pipe(plugins.sass())
            .pipe(plugins.postcss([
                plugins.autoprefixer({
                    browsers: ['last 10 versions', 'ie >= 8']
                })
            ]))
            .pipe(gulp.dest(process.env.SCSS_DEST))
            .on('error', plugins.log.error);
    });

    gulp.task('scss-enhanced', () => {
        return gulp.src(process.env.SCSS_SRC + 'enhanced.scss')
            .pipe(plugins.sass())
            .pipe(plugins.postcss([
                plugins.autoprefixer({
                    browsers: ['last 10 versions', 'ie >= 8']
                })
            ]))
            .pipe(gulp.dest(process.env.SCSS_DEST))
            .on('error', plugins.log.error);
    });

    gulp.task('scss-minify', () => {
        return gulp.src([
            process.env.SCSS_DEST + 'core.css',
            process.env.SCSS_DEST + 'enhanced.css'
        ])
            .pipe(plugins.postcss([
                plugins.cssnano({
                    preset: 'default'
                })
            ]))
            .pipe(gulp.dest(process.env.SCSS_DEST))
            .on('error', plugins.log.error);
    });
};
