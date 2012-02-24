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
set :copy_exclude, [".svn", ".git", "envaya.org.key"]
set :user, "root"

role :web, "www.envaya.org"                          # Your HTTP server, Apache/etc
role :app, "www.envaya.org"                          # This may be the same as your `Web` server
role :db,  "www.envaya.org", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

namespace :deploy do
    task :default do
        sanity_check
        update
        restart
        #update_test
        update_translations
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
    
    task :update_test do
        test_dir = "#{deploy_to}/test"
        
        run "rsync -az --delete --exclude='config/local.php' --chmod=u+rx,g+rx,o+rx #{current_path}/ #{test_dir}"
        run "cp #{shared_path}/local_test.php #{test_dir}/config/local.php"
    end
        
    # sets up a single server with all of envaya's services installed
    task :allinone_setup do
        basic_setup
        web_setup
        db_setup
        kestrel_setup
        queue_setup
        sphinx_setup
        memcached_setup
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
        memcached_setup
        sphinx_setup
    end
    
    task :sanity_check do
    
        files = [
            "build/cache.php",
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
            "www/_media/uploader.js",
            "www/_media/tiny_mce.js",
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
    
    task :cert_setup do
        cert_dir = "/etc/nginx/ssl"
        cert_path = "#{cert_dir}/envaya_combined.crt"
        key_path = "#{cert_dir}/envaya.org.key"
    
        begin
            run "stat -t #{key_path}"
        rescue Exception    
            top.upload(File.join(Dir.pwd, "ssl/envaya.org.key"), key_path)
            run "chmod 400 #{key_path}"
        end

        begin
            run "stat -t #{cert_path}"
        rescue Exception    
            top.upload(File.join(Dir.pwd, "ssl/envaya_combined.crt"), cert_path)            
            run "chmod 644 #{cert_path}"
        end
        
        begin
            run "stat -t /etc/nginx/sites-enabled/ssl"
        rescue Exception    
            run "ln -s /etc/nginx/sites-available/ssl /etc/nginx/sites-enabled/ssl"
            run "nginx -t"
            run "/etc/init.d/nginx reload"
        end
    end
    
    task :php_setup do
        run "#{current_path}/scripts/setup/php.sh"
    end
    
    task :memcached_setup do
        run "#{current_path}/scripts/setup/memcached.sh"
    end    
    
    task :db_setup do
        run "#{current_path}/scripts/setup/mysql.sh"
        run "cd #{current_path} && (php scripts/db_setup.php | mysql)"
        run "cd #{current_path} && php scripts/install_tables.php"
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
        
        # don't use /var/envaya/current symlink in nginx conf because php5-fpm 
        # won't update to the new target of the symlink unless it's restarted 
        # (which could result in a few seconds of downtime). 
        # This allows us to simply reload the nginx config without downtime.
        run "mkdir -p /etc/nginx && echo \"root #{latest_release}/www;\" > /etc/nginx/root.conf"
    end
    
    task :update_code, :except => { :no_release => true } do
        strategy.deploy!
        finalize_update
    end    
    
    task :update_translations do
        run "php #{current_path}/mod/translate/scripts/update_translations.php"
    end
    
    task :restart, :roles => :app, :except => { :no_release => true } do        
        run "/etc/init.d/phpCron restart"
        run "/etc/init.d/queueRunner restart"
        run "nginx -t"
        run "/etc/init.d/nginx reload"
        run "rm -rf /var/nginx/cache/envaya"
    end
end
