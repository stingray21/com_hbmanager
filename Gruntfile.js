module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sync: {
            source: {
                files: [{
                    cwd: '<%= ext.joomla %><%= ext.package %><%= ext.name %>/', // makes all src relative to cwd
                    //cwd: '../../../xampp/htdocs/handball/hb_joomla3/' + function(){console.log('test'); return 'zzz_packages/';} + 'mod_tsvevents/',
                    //cwd: '../../../xampp/htdocs/handball/hb_joomla3/' + 'zzz_packages/' + 'mod_tsvevents/',
                    src: [
                        '**'
                    ],
                    dest:  './<%= ext.name %>/',  
                }],
                pretend: false, // Don't do any IO. Before you run the task with `updateAndDelete` PLEASE MAKE SURE it doesn't remove too much. 
                verbose: false // Display log messages when copying files 
            }
        },
        'string-replace': {
            version: {
                files: {
                    './<%= ext.name %>/': './<%= ext.name %>/<%= ext.name %>.xml',    // 'a': 'b' means b is source file and a is destination file 
                },
                options: {
                    replacements: [{
                            pattern: /<version>.+?<\/version>/g,
                            replacement: '<version><%= pkg.version %></version>'
                        },
                        {
                            pattern: /<creationDate>.+?<\/creationDate>/g,
                            replacement: '<creationDate><%= pkg.date %></creationDate>'
                        }]
                }
            }
        },
        bumpup: {
            options: {
                dateformat: 'YYYY-MM-DD HH:mm Z',
                normalize: false,
                updateProps: {
                    pkg: 'package.json'
                }
            },
            setters: {
                // Overwrites setter for `date` property 
                date: function (old, releaseType, options) {
                    return grunt.template.today('yyyy-mm-dd HH:MM Z');   // local timezone 
                },
            },
            files: ['package.json']
        },
        compress: {
            main: {
                options: {
                  archive: '../Releases/<%= ext.name %>_' + grunt.template.today('yyyymmdd_HHMMss') + '.zip'
                },
                files: [
                    {
                    expand: true,
                    cwd: '<%= ext.name %>',
                    src: '**/*',
                    },
                ]
            }
        },

    });

    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-sync');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-bumpup');

    // load path from separate json file that is not synced with git
    grunt.config.set('ext', grunt.file.readJSON('ext.json'));
    // --> ext.json => e.g.
    // {
    //     "joomla": "D:\\xampp\\htdocs\\handball\\hb_joomla3\\",
    //     "package": "zzz_packages/"
    // }

    /// ------------

    grunt.registerTask('default', ['compress']);
    grunt.registerTask('import', ['run-grunt', 'sync:source']);


    grunt.registerTask('build_sub', 'Build Joomla extension', function(build) {
        if (build === undefined) {
            // grunt.warn('Building patch (build:patch)');
            grunt.log.writeln('Building patch (build:patch)');
            build = 'patch';
        }
        grunt.log.writeln('Build ' + build + ' version');
            
        grunt.task.run('bumpup:' + build);
        grunt.task.run('string-replace:version');
        grunt.task.run('compress');
    });


    grunt.registerTask('build', 'Import and build Joomla extension', function(build) {
        if (build === undefined) {
            // grunt.warn('Building patch (build:patch)');
            grunt.log.writeln('Building patch (build:patch)');
            build = 'patch';
        }
        grunt.log.writeln('Build ' + build + ' version');
        
        grunt.task.run('import');    
        grunt.task.run('bumpup:' + build);
        grunt.task.run('string-replace:version');
        grunt.task.run('compress');
    });



    // run grunt file in joomla folder
    grunt.registerTask('run-grunt', function() {
        var ext = require('./ext.json');
        // console.log(paths);
        var cb = this.async();
        var child = grunt.util.spawn({
            grunt: true,
            args: ['build_' + ext.name],
            opts: {
                cwd: ext.joomla
                }
        }, function(error, result, code) {
          cb();
        });

        child.stdout.pipe(process.stdout);
        child.stderr.pipe(process.stderr);
    });



};
