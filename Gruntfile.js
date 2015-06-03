/**
 * Gruntfile.js
 */
module.exports = function (grunt) {
    grunt.initConfig({
        phpcs: {
            all: {
                dir: ['*.php', 'includes/*.php', 'admin/*.php', 'front/*.php'],
                options: {
                    bin: 'vendor/bin/phpcs',
                    standard: 'ruleset.xml'
                }
            }
        },
        phplint: {
            all: ['*.php', 'includes/*.php', 'admin/*.php', 'front/*.php']
        }
    });

    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phplint');
    grunt.registerTask('default', ['phplint:all', 'phpcs:all']);
};