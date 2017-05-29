module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        joomla_packager: {
            com_hbteam: {
                options: {
                  'name': 'hbteam',
                  'type': 'component',
                  //'joomla': '/home/josh/lamp/public_html/handball/hb_joomla3',
                  'joomla': 'D:/xampp/htdocs/handball/hb_joomla3', // win Asus
                  //'dest': '/home/josh/joomla-extensions/com_hbmanager/Source'      
                  'dest': '/com_hbmanager/Source' // win Asus       
                }
            }
        },
        compress: {
            main: {
                options: {
                  //archive: '../Releases/com_hbteam.zip'
                  archive: '../Releases/com_hbmanager_' + grunt.template.today('yyyymmdd_HHMMss') + '.zip'
                },
                files: [
                    {
                    expand: true,
                    cwd: 'com_hbmanager',
                    src: '**/*',
                    },
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-joomla-packager');
    grunt.loadNpmTasks('grunt-contrib-compress');
    // grunt.loadNpmTasks('grunt-contrib-uglify');
    // grunt.loadNpmTasks('grunt-contrib-cssmin');
    // grunt.loadNpmTasks('grunt-spritesmith');
    // grunt.loadNpmTasks('grunt-contrib-copy');
    // grunt.loadNpmTasks('grunt-contrib-watch');
    // grunt.loadNpmTasks('grunt-sass');
    // grunt.loadNpmTasks('grunt-contrib-clean');
    // grunt.loadNpmTasks('grunt-postcss');
    // grunt.loadNpmTasks('grunt-contrib-concat');


    grunt.registerTask('default', ['compress']);
    grunt.registerTask('build', ['joomla_packager:com_hbteam', 'compress']);
    // grunt.registerTask('build', ['clean:build', 'concat:main', 'uglify:build', 'sass:build', 'postcss:dist', 'cssmin', 'copy:build', 'copy:buildmin']);
    // grunt.registerTask('dev', ['clean:build', 'concat:main', 'sass:dev', 'postcss:dist', 'copy:dev']);
    // grunt.registerTask('devplusmin', ['clean:build', 'concat:main', 'sass:dev', 'postcss:dist', 'copy:dev', 'uglify:build', 'sass:build', 'postcss:dist', 'cssmin', 'copy:buildmin']);
    // grunt.registerTask('sprite-all', ['sprite:all']);
    // grunt.registerTask('compsass', ['sass:dev', 'postcss:dist']);

};
