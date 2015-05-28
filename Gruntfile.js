/* jshint globalstrict:true */
'use strict';

module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        srcdir: 'src',
        testdir: 'tests',
        watch: {
            phptest: {
                files: '<%= testdir %>/**/*.php',
                tasks: ['phplint', 'phpcsfixer:find', 'phpunit']
            },
            phpsrc: {
                files: '<%= srcdir %>/**/*.php',
                tasks: ['phplint', 'phpcsfixer:find', 'phpunit']
            }
        },
        php: {
            dist: {
                options: {
                    port: 9000
                }
            }
        },
        phplint: {
            all: ['<%= srcdir %>/**/*.php']
        },
        phpunit: {
            unit: {
                dir: '<%= testdir %>/'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                bootstrap: 'vendor/autoload.php',
                colors: true//,
                // testdox: true
            }
        },
        phpcsfixer: {
            options: {
                level: 'all'
            },
            fix: {
                dir: '<%= srcdir %>'
            },
            find: {
                options: {
                    verbose: true,
                    dryRun: true,
                    diff: true
                },
                dir: '<%= srcdir %>'
            }
        }
    });

    grunt.registerTask('dev', 'Set up dev environment for PHP code', [
        // 'php:dist',
        'watch'
    ]);

    // grunt.registerTask('serve', ['php']);
};