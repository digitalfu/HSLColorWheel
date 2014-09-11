/**
 * Created by Hamish on 7/09/14.
 */

/**
 * Gruntfile.js
 */
module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        php: {
            dist: {
                options: {
                    port: 8080,
                    base: 'web',
                    open: true,
                    keepalive: true
                }
            }
        },
        phpcs: {
            application: {
                dir: 'src'
            },
            options: {
                bin: 'phpcs',
                standard: 'PSR-MOD'
            }
        },
        phplint: {
            options: {
                swapPath: '/tmp'
            },
            all: ['src/*.php', 'src/base/*.php', 'src/config/*.php', 'src/controller/*.php', 'src/model/*.php']
        },
        phpunit: {
            unit: {
                dir: 'tests/unit'
            },
            options: {
                bin: 'phpunit',
                bootstrap: 'tests/Bootstrap.php',
                colors: true,
                testdox: true
            }
        },
        php_analyzer: {
            application: {
                dir: 'src'
            }
        }
    });

    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-phplint');
    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-php-analyzer');
    grunt.registerTask('precommit', ['phplint:all', 'phpunit:unit']);
    grunt.registerTask('default', ['phplint:all', 'phpcs', 'phpunit:unit', 'php_analyzer:application']);
    grunt.registerTask('server', ['php']);
};
