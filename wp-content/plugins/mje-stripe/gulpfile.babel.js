import gulp from 'gulp';
import sass from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import plumber from 'gulp-plumber';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify';
import rename from 'gulp-rename';
import zip from 'gulp-zip';
import browserSync from 'browser-sync';
import gutil from 'gulp-util';
//import replace from 'gulp-replace';
import bump from 'gulp-bump';
import git from 'gulp-git';

// Configuration
const config = {
  dev: {
    proxyDomain: 'mje.dev',
    port: 8000
  },
  js: {
    src: [
      'dev/js/**/*.js'
    ],
    fileName: 'mje-stripe.js',
    dest: 'assets/js'
  },
  style: {
    src: [
      'dev/sass/**/*.scss',
    ],
    dest: 'assets/css',
  },
  zip: {
    src: [
      '**',
      '!gulpfile.babel.js',
      '!package.json',
      '!README.md',
      '!yarn.lock',
      '!.*',
      '!.*/**',
      '!node_modules',
      '!node_modules/**',
      '!dev',  // do not include folder development
      '!dev/**',
      '!composer.json',
      '!composer.lock',
    ],
    fileName: 'mje-stripe-v{version}.zip',
    dest: './'
  },
  sourceFiles: ['**/*.html', '**/*.php'] // handle files .html, .php change
};

var onError = (err) => {
  console.log('An error occurred: ' + err.message);
  gutil.beep();
  this.emit('end');
};

/**
 * STYLE TASKS
 */
gulp.task('sass', () => {
  return gulp.src(config.style.src)
    .pipe(plumber({ errorHandler: onError }))
    .pipe(sass())
    .pipe(autoprefixer()) // auto add prefixer
    .pipe(gulp.dest(config.style.dest))
    .pipe(rename((path) => { // rename to .min.css
      path.basename += '.min';
    }))
    .pipe(sass({ outputStyle: 'compressed' })) // compress css
    .pipe(gulp.dest(config.style.dest))
    .pipe(browserSync.stream());
});

/**
 * JS TASKS
 */
gulp.task('js', () => {
  return gulp.src(config.js.src)
    .pipe(plumber({ errorHandler: onError }))
    .pipe(concat(config.js.fileName)) // concatenate js
    .pipe(gulp.dest(config.js.dest))
    .pipe(rename((path) => { // rename to .min.js
      path.basename += '.min';
    }))
    .pipe(uglify()) // compress js
    .pipe(gulp.dest(config.js.dest))
});

// RELOAD TASKS
gulp.task('reload', (done) => {
  browserSync.reload();
  done();
});

gulp.task('js-reload', ['js'], (done) => {
  browserSync.reload();
  done();
});

/**
 * GROUP TASKS FOR DEVELOPMENT
 */
gulp.task('dev', ['sass', 'js'], () => {
  browserSync.init({
    proxy: config.dev.proxyDomain,
    ui: {
      port: config.dev.port
    },
    open: false,
  });

  gulp.watch(config.style.src, ['sass']);
  gulp.watch(config.js.src, ['js-reload']);
});

/**
 * GROUP TASK FOR BUILDING
 */
gulp.task('build', ['sass', 'js']);

/**
 * GROUP TASK FOR RELEASE
 */
// Bump version
gulp.task('bump-version', (done) => {
  if(typeof gutil.env.version !== 'undefined') {
  gulp.src(['./package.json', './mje-stripe.php'])
    .pipe(plumber({ errorHandler: onError }))
    .pipe(bump({ version: gutil.env.version }))
    .pipe(gulp.dest('./'));

  done();
} else {
  done('Missing argument --version=1.0.0');
}
});

// Zip theme
gulp.task('zip', () => {
  var version = (typeof gutil.env.version !== 'undefined') ? gutil.env.version : '1.0';
var fileName = config.zip.fileName.replace('{version}', version);
return gulp.src(config.zip.src)
  .pipe(zip(fileName))
  .pipe(gulp.dest(config.zip.dest));
});

// Create changelog
gulp.task('changelog', () => {
  var preVersion = (typeof gutil.env.preversion !== 'undefined') ? gutil.env.preversion : '1.0';
var version = (typeof gutil.env.version !== 'undefined') ? gutil.env.version : '1.0';
git.exec({ args: 'diff --name-only --diff-filter=AM MjE-Stripe-v' + preVersion + ' HEAD > changelog-v' + version + '.txt' }, (err, stdout) => {
  if (err) throw err;
})
});

gulp.task('release', ['sass', 'js', 'bump-version', 'changelog', 'zip']);

// DEFAULT TASKS
gulp.task('default', ['sass', 'js']);