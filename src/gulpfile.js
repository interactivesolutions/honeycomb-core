let gulp = require("gulp"),
    uglify = require("gulp-uglify"),
    minify = require("gulp-minify-css"),
    ttf2woff2 = require("gulp-ttf2woff2"),
    concat = require("gulp-concat");

gulp.task("default", function () {
    generate(false);
});

gulp.task("live", function () {
    generate(true);
});

function generate(min) {

    gulp.src(["node_modules/font-awesome/fonts/*.ttf"])
        .pipe(ttf2woff2())
        .pipe(gulp.dest("public/fonts/"));

    generateJS(
        "external",
        [
            "node_modules/jquery/dist/jquery.js",
            "node_modules/bootstrap/dist/js/bootstrap.js",
            "node_modules/admin-lte/dist/js/adminlte.js",

        ],
        min);

    generateJS(
        "hc-shared",
        [
            "resources/assets/honeycomb/js/shared/hc-helpers.js",
            "resources/assets/honeycomb/js/shared/hc-functions.js",
            "resources/assets/honeycomb/js/shared/hc-objects.js",
            "resources/assets/honeycomb/js/shared/hc-loader.js",
            "resources/assets/honeycomb/js/shared/hc-service.js",
        ],
        min);

    generateJS(
        "hc-form",
        [
            "resources/assets/honeycomb/js/form/hc-form-manager.js",
            "resources/assets/honeycomb/js/form/hc-form.js",
            "resources/assets/honeycomb/js/form/hc-form-basic-field.js",
            "resources/assets/honeycomb/js/form/hc-form-button.js",
            "resources/assets/honeycomb/js/form/hc-form-single-line.js",
            "resources/assets/honeycomb/js/form/hc-form-email.js",
            "resources/assets/honeycomb/js/form/hc-form-password.js",
            "resources/assets/honeycomb/js/form/hc-form-date-time-picker.js",
            "resources/assets/honeycomb/js/form/hc-form-text-area.js",
            "resources/assets/honeycomb/js/form/hc-form-rich-text-area.js",
            "resources/assets/honeycomb/js/form/hc-form-check-box-list.js",
            "resources/assets/honeycomb/js/form/hc-form-radio-list.js",
            "resources/assets/honeycomb/js/form/hc-form-drop-down-list.js",
            "resources/assets/honeycomb/js/form/hc-form-upload-file.js",
            "resources/assets/honeycomb/js/form/hc-form-google-map.js",
            "resources/assets/honeycomb/js/popup/hc-popup.js"
        ],
        min);

    generateJS(
        "hc-admin-list",
        [
            "resources/assets/honeycomb/js/list/hc-core-list.js",
            "resources/assets/honeycomb/js/list/hc-simple-list.js",
            "resources/assets/honeycomb/js/list/types/hc-endless.js",
        ],
        min);

    generateCSS(
        "hc-admin-panel",
        [
            "node_modules/font-awesome/css/font-awesome.css",
            "node_modules/bootstrap/dist/css/bootstrap.css",
            "node_modules/admin-lte/dist/css/AdminLTE.css",
            "node_modules/admin-lte/dist/css/skins/skin-blue.css"
        ]
    )
}

function generateJS(output, list, min) {
    if (min)
        return gulp.src(list).pipe(uglify()).pipe(concat(output + ".js")).pipe(gulp.dest("public/js/")).on("error", handleError);
    else
        return gulp.src(list).pipe(concat(output + ".js")).pipe(gulp.dest("public/js/")).on("error", handleError);
}

function generateCSS(output, list, min) {
    if (min)
        return gulp.src(list).pipe(minify()).pipe(concat(output + ".css")).pipe(gulp.dest("public/css/")).on("error", handleError);
    else
        return gulp.src(list).pipe(concat(output + ".css")).pipe(gulp.dest("public/css/")).on("error", handleError);
}

function handleError(err) {
    console.log(err.toString());
    process.exit(-1);
}