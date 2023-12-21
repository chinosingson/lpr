const { series, parallel, src, dest, watch } = require('gulp'),
  sourcemaps = require('gulp-sourcemaps'),
  ext = require('gulp-ext-replace'),
  inputs = {
    'js': 'src/js/**/*.js',
  }

const css = () => {
  return src('src/css/*.scss')
    .pipe(sourcemaps.init())
    .pipe(ext('.min.css'))
    .pipe(require('gulp-postcss')([
      require('postcss-import'),
      require('postcss-nested'),
      require('autoprefixer'),
      require('cssnano')
    ]))
    .pipe(sourcemaps.write('.'))
    .pipe(dest('dist/css'))
}

const js = () => {
  return src(inputs.js)
    .pipe(sourcemaps.init())
    .pipe(ext('.min.js', '.es6.js'))
    .pipe(require('gulp-babel')({
      presets: [['@babel/preset-env', {
        modules: false
      }]]
    }))
    .pipe(require('gulp-uglify')())
    .pipe(sourcemaps.write('.'))
    .pipe(dest('dist/js'))
}

const clean = () => {
  return require('del')('dist')
}

exports.default = exports.watch = () => {
  watch(['src/css/**/*.scss', 'tailwind.config.js'], css)
  watch([inputs.js], js)
}

exports.build = series(
  clean,
  parallel(
    css,
    js,
  )
)
