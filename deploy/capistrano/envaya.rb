#
# Capistrano deployment script for releasing code on envaya.org or a similar test server,
# or configuring a new remote production/test server.
#
# Update latest code on 1.2.3.4:
# cap HOSTS=1.2.3.4 deploy
# 
# Configure a new server:
# cap HOSTS=1.2.3.4 deploy:full_setup
#
#

default_run_options[:pty] = true

set :application, "envaya"
set :deploy_to, "/var/envaya"
set :deploy_via, :rsync_with_remote_cache
set :copy_exclude, [".svn", ".git", "yuicompressor-2.4.2.jar", "kestrel_dev", "selenium-server.jar"]
set :user, "root"

role :web, "www.envaya.org"                          # Your HTTP server, Apache/etc
role :app, "www.envaya.org"                          # This may be the same as your `Web` server
role :db,  "www.envaya.org", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

namespace :deploy do
    task :default do
        sanity_check
        backup_db
        update
        restart
    end
    
    task :full_setup do                
        pre_setup
        setup
        localsettings_setup       
        sanity_check        
        update
        db_install
        post_setup
    end   
    
    task :sanity_check do
    
        files = [
            "build/lib_cache.php",
            "build/path_cache.php",
            "www/_media/css/home.css",
            "www/_media/css/simple.css",
            "www/_media/css/green.css",
            "www/_media/css/craft1.css",
            "www/_media/css/brick.css",
            "www/_media/css/editor.css",
            "www/_media/css/tinymce_ui.css",
            "www/_media/css/mobile.css",
            "www/_media/inline/header.js",
            "www/_media/inline/xhr.js",
            "www/_media/inline/dom.js",
            "www/_media/inline/language.js",
            "www/_media/swfupload.js",
            "www/_media/tiny_mce/tiny_mce.js",
        ]
    
        files.each do |file|
            if !File.file?(File.join(Dir.pwd, file))
                throw "sanity check failed, missing #{file}; run make.php"
            end
        end
        
        print "sanity check passed"
    end
    
    task :localsettings_setup do    
        begin
            run "stat -t #{shared_path}/local.php"
        rescue Exception    
            top.upload(File.join(Dir.pwd, "config/production.php"), "#{shared_path}/local.php")
        end
    end
    
    task :db_install do
        run "cd #{current_path} && (php scripts/db_setup.php | mysql)"
        run "cd #{current_path} && php scripts/install.php"
    end
    
    task :dropbox_setup do
        run "/var/envaya/current/scripts/server_dropbox_setup.sh"
    end
    
    task :pre_setup do
        top.upload(File.join(Dir.pwd, "scripts/server_pre_setup.sh"), "/root/server_pre_setup.sh")
        run "chmod 744 /root/server_pre_setup.sh"
        run "/root/server_pre_setup.sh"
    end
    
    task :post_setup do
        run "/var/envaya/current/scripts/server_setup.sh /var/envaya/current"
        run "/var/envaya/current/scripts/server_extras_setup.sh"
    end
        
    task :backup_db, :roles => :app, :except => { :no_release => true } do            
        run "cd #{current_path} && php scripts/backup.php"
    end    

    task :finalize_update, :except => { :no_release => true } do
        run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
        run "cp #{shared_path}/local.php #{latest_release}/config/local.php"
    end
    
    task :restart, :roles => :app, :except => { :no_release => true } do        
        run "/etc/init.d/phpCron restart"
        run "/etc/init.d/queueRunner restart"
        run "/etc/init.d/php5-fpm reload"
        run "rm -rf /var/nginx/cache/envaya"
    end
end
