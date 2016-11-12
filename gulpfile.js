var gulp       = require('gulp');
var mkdirp     = require('mkdirp');
var fs         = require('fs-extra');
var del        = require('del');
var zip        = require('gulp-zip');
var seq        = require('run-sequence');
var glob       = require('glob');
var gulpcb     = require('gulp-callback');
var sass       = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var bourbon    = require('node-bourbon');
var gulpif     = require('gulp-if');

var moduleName = __dirname.split('/').pop();
var useSourcemaps = false;

moduleName = 'mymodule';

var tmpRoot   = './tmp/';
var tmpFolder = tmpRoot + moduleName;
var srcZip = [
  './**',
  '!./tmp/',
  '!./tmp/**/*',
  '!./sass-cache/',
  '!./sass-cache/**/*',
  '!./node_modules/',
  '!./node_modules/**',
  '!./.editorconfig',
  '!./.gitattributes',
  '!./.gitignore',
  '!./_methods.php',
  '!./config.rb',
  '!./gulpfile.js',
  '!./package.json',
  '!./views/**/*.css.map',
  '!./*.zip'
];
var excludeIndex = [];

/**
 * Creates temporary folder where .zip archive files will be collected
 */
gulp.task('make-tmp', function() {
  return mkdirp(tmpFolder);
});

/**
 * Removes temporary folder
 */
gulp.task('remove-tmp', function() {
  return del(tmpRoot);
});

/**
 * Collects .zip archive files defined with glob pattern in 'srcZip' variable to temporary folder.
 */
gulp.task('collect-files', function() {
  return gulp.src(srcZip).pipe(gulp.dest(tmpFolder));
});

/**
 * Scans for module version inside .php file and creates an archive from the files inside temporary folder
 */
gulp.task('create-zip', function(callback) {
  getModuleVersion(function(version) {
    gulp.src(tmpFolder + '*/**')
      .pipe(zip('v' + version + '-' + moduleName +'.zip'))
      .pipe(gulp.dest('./'))
      .pipe(gulpcb(function() {
        callback && callback();
      }));
  });
});

/**
 * Compile .scss files to .css
 */
gulp.task('compile-css', function() {
  return gulp
    .src('./views/sass/**/*.scss')
    .pipe(
      sass({
        includePaths: bourbon.includePaths,
        outputStyle: 'expanded',
        precision: 8
      }).on('error', sass.logError)
    )
    .pipe(gulpif(useSourcemaps, sourcemaps.init()))
    .pipe(gulpif(useSourcemaps, sourcemaps.write('./')))
    .pipe(gulp.dest('./views/css/'));
});

/**
 * Copies index.php from root dir to all folder and subfolders in temporary folder
 */
gulp.task('copy-index', function(callback) {
  var total;
  var done = 0;
  glob(tmpFolder + '/**/', { ignore : excludeIndex }, function(err, folders) {
    total = folders.length;
    if (total < 1) {
      callback && callback();
    }

    folders.forEach(function(folder) {
      fs.copy('index.php', folder + '/index.php', function(err) {
        done++;
        if (err) { return console.error(err); }

        if (done == total) {
          callback && callback();
        }
      });
    });
  });
});

/**
 * List smarty translations {l s='Translatable Text' mod='mymodule'}
 * which have incorrect context (mod parameter)
 */
gulp.task('scan-translations', function (cb) {
  glob('./**/*.tpl', function (er, files) {
    var brokenTranslations = [];
    var totalFiles = files.length;
    var scannedFiles = 0;

    files.forEach(function(file) {
      var translationContext = moduleName;

      fs.readFile(file, 'utf-8', function (err, contents) {
        var brokenTranslationsInFile = listBrokenTranslationStrings(contents, translationContext);
        if (brokenTranslationsInFile.length) {
          brokenTranslationsInFile.unshift(file);
          brokenTranslations.push(brokenTranslationsInFile);
        }

        if (++scannedFiles == totalFiles) {
          console.log('Broken Smarty translation strings in module templates:');
          console.log(brokenTranslations);
          cb && cb();
        }
      });
    });

  });
});

/**
 * Runs gulp tasks in a defined sequence.
 * @see https://www.npmjs.com/package/run-sequence
 */
gulp.task('build', function(callback) {
  seq(
    'remove-tmp',
    'make-tmp',
    'compile-css',
    'collect-files',
    'copy-index',
    'create-zip',
    'remove-tmp',
    callback
  );
});

/**
 * Defines default task when you type 'gulp' in the console
 */
gulp.task('default', ['build']);

/**
 * Returns module version defined in mymodule.php:
 * $this->version = '1.0.0'
 *
 * @param callback
 */
function getModuleVersion(callback) {
  fs.readFile('./' + moduleName +'.php', 'utf8', function (err, data) {
    if (err) { return console.error(err); }

    var version = '0.0.0';
    var matches = data.match(/\$this->version\s*=\s*['"]([\d\.]+)['"]\s*;/i);

    if (matches !== null && typeof matches[1] == 'string') {
      version = matches[1];
    }

    callback && callback(version);
  });
}

function listBrokenTranslationStrings(smartyTplCode, translationContext) {
  var translations = smartyTplCode.match(/\{l\s+s=['"].+?}/g);

  if (!translations) {
    return [];
  }

  return translations.filter(function(t) {
    if (translationContext.length) {
      // Doesn't have correct context
      return !((new RegExp('mod=[\'"]' + translationContext + '[\'"]')).test(t));
    } else {
      // Has context even though it shouldn't
      return !!/mod=['"]/.test(t);
    }
  });
}
