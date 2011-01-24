
default_run_options[:pty] = true

set :application, "envaya"
set :repository,  "git@anvaya.unfuddle.com:anvaya/envaya.git"
set :deploy_to, "/var/envaya"
set :deploy_via, :remote_cache
set :copy_exclude, [".svn", "settings.php", "selenium-server.jar"]

set :user, "root"
set :scm, :git

# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "www.envaya.org"                          # Your HTTP server, Apache/etc
role :app, "www.envaya.org"                          # This may be the same as your `Web` server
role :db,  "www.envaya.org", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

namespace :deploy do
    task :default do
        backup_db
        update
        restart
    end
    
    task :full_setup do                
        pre_setup
        setup
        ssh_key_setup
        localsettings_setup       
        update
        db_install
        post_setup
    end
    
    task :localsettings_setup do    
        begin
            run "stat -t #{shared_path}/settings.php"
        rescue Exception    
            top.upload(File.join(Dir.pwd, "engine/settings_vps.php"), "#{shared_path}/settings.php")
        end
    end
    
    task :db_install do
        run "cd #{current_path} && (php scripts/db_setup.php | mysql)"
        run "cd #{current_path} && php scripts/install.php"
    end
    
    task :dropbox_setup do
        top.upload(File.join(Dir.pwd, "scripts/server_dropbox_setup.sh"), "/root/server_dropbox_setup.sh")    
        run "chmod 744 /root/server_dropbox_setup.sh"
        run "/root/server_dropbox_setup.sh"
    end
    
    task :pre_setup do
        top.upload(File.join(Dir.pwd, "scripts/server_pre_setup.sh"), "/root/server_pre_setup.sh")
        run "chmod 744 /root/server_pre_setup.sh"
        run "/root/server_pre_setup.sh"
    end
    
    task :post_setup do
        top.upload(File.join(Dir.pwd, "scripts/server_setup.sh"), "/root/server_setup.sh")
        run "chmod 744 /root/server_setup.sh"
        run "/root/server_setup.sh /var/envaya/current"
    end
    
    task :ssh_key_setup do
        run "if [ ! -e ~/.ssh/id_rsa ]; then ssh-keygen -f ~/.ssh/id_rsa -N ''; fi"       
        local_file = File.join(Dir.pwd, "id_rsa.pub")
        top.download("/root/.ssh/id_rsa.pub", local_file)        
        prompt = "Copy the contents of ssh public key in #{local_file} to unfuddle personal settings to allow server to access git. Enter ok when complete"
        k = Capistrano::CLI.ui.ask(prompt) do |q|
            q.overwrite = false
            q.validate = /ok/i
            q.responses[:not_valid] = "?"
        end 
        run "echo done"
    end
    
    task :backup_db, :roles => :app, :except => { :no_release => true } do            
        run "cd #{current_path} && php scripts/backup.php"
    end    

    task :finalize_update, :except => { :no_release => true } do
        run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
        run "cp #{shared_path}/cached-copy/.htaccess #{latest_release}/"
        run "cp #{shared_path}/settings.php #{latest_release}/engine/settings.php"
    end
    
    task :restart, :roles => :app, :except => { :no_release => true } do        
        run "rm -rf /var/nginx/cache/envaya"
        run "/etc/init.d/phpCron restart"
        run "/etc/init.d/queueRunner restart"
        run "/etc/init.d/php5-fpm restart"
    end
end
