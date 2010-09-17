
default_run_options[:pty] = true

set :application, "envaya"
set :repository,  "git@anvaya.unfuddle.com:anvaya/envaya.git"
set :deploy_to, "/var/envaya"
set :deploy_via, :remote_cache
set :copy_exclude, [".svn", "localsettings.php", "selenium-server.jar"]

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
    
    task :backup_db, :roles => :app, :except => { :no_release => true } do            
        run "cd #{current_path} && php scripts/backup.php"
    end    

    task :finalize_update, :except => { :no_release => true } do
        run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
        run "cp #{shared_path}/cached-copy/.htaccess #{latest_release}/"
        run "cp #{shared_path}/localsettings.php #{latest_release}/engine/localsettings.php"
    end
    
    task :restart, :roles => :app, :except => { :no_release => true } do        
        run "/etc/init.d/phpCron restart"
        run "/etc/init.d/queueRunner restart"
    end
end
