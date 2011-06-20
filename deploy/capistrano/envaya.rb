#
# Capistrano deployment script for releasing code on envaya.org or a similar test server,
# or configuring a new remote production/test server.
#
# Update latest code on 1.2.3.4:
# cap HOSTS=1.2.3.4 deploy
# 
# Configure a new server with all of Envaya's services:
# cap HOSTS=1.2.3.4 deploy:allinone_setup
#
#

default_run_options[:pty] = true

set :application, "envaya"
set :deploy_to, "/var/envaya"
set :deploy_via, :rsync_with_remote_cache
set :copy_exclude, [".svn", ".git", "selenium-server.jar"]
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
    
    # sets up a server with envaya's code, but no particular services installed 
    task :basic_setup do                
        pre_setup
        setup
        localsettings_setup
        sanity_check
        update
        php_setup
        dataroot_setup
    end   
        
    # sets up a single server with all of envaya's services installed
    task :allinone_setup do
        basic_setup
        web_setup
        db_setup
        kestrel_setup
        queue_setup
        sphinx_setup
        cron_setup        
        extras_setup
        upgrade
    end
    
    task :test_setup do
        basic_setup
        web_setup
        db_setup
        kestrel_setup
        queue_setup
        sphinx_setup
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
    
    task :php_setup do
        run "#{current_path}/scripts/setup/php.sh"
    end
    
    task :db_setup do
        run "#{current_path}/scripts/setup/mysql.sh"
        run "cd #{current_path} && (php scripts/db_setup.php | mysql)"
        run "cd #{current_path} && php scripts/install_tables.php"
    end
    
    task :dropbox_setup do
        run "#{current_path}/scripts/setup/dropbox.sh"
    end
    
    task :pre_setup do
	top.upload(File.join(Dir.pwd, "scripts/setup/sources.sh"), "/root/sources.sh")    
        top.upload(File.join(Dir.pwd, "scripts/setup/prereqs.sh"), "/root/prereqs.sh")
        run "chmod 744 /root/sources.sh /root/prereqs.sh"
        run "/root/sources.sh"
        run "/root/prereqs.sh"
    end
    
    task :dataroot_setup do
        run "cd #{current_path} && php scripts/install_dataroot.php"
    end
    
    task :web_setup do
        run "#{current_path}/scripts/setup/nginx.sh"
    end
        
    task :upgrade do
        run "#{current_path}/scripts/setup/upgrade.sh"
    end

    task :kestrel_setup do
        # todo rsync kestrel .jar and libs
        run "#{current_path}/scripts/setup/kestrel.sh"
    end
    
    task :sphinx_setup do
        run "#{current_path}/scripts/setup/sphinx.sh"        
        run "#{current_path}/scripts/setup/sphinx_service.sh"        
    end

    task :extras_setup do
        run "#{current_path}/scripts/setup/extras.sh"
    end
    
    task :queue_setup do
        run "#{current_path}/scripts/setup/queue.sh"
    end 
    
    task :cron_setup do
        run "#{current_path}/scripts/setup/cron.sh"
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
