
set :application, "envaya"
set :repository,  "http://anvaya.unfuddle.com/svn/anvaya_anvaya"
set :deploy_to, "/var/envaya"
set :deploy_via, :remote_cache
set :copy_exclude, [".svn", "localsettings.php", "selenium-server.jar"]

set :user, "root"

set :scm, :subversion
set :scm_username, "youngj"
set :scm_password, "LssP12"

# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "www.envaya.org"                          # Your HTTP server, Apache/etc
role :app, "www.envaya.org"                          # This may be the same as your `Web` server
role :db,  "www.envaya.org", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

namespace :deploy do
    task :finalize_update, :except => { :no_release => true } do
        run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
        run "cp #{shared_path}/cached-copy/.htaccess #{latest_release}/"
        run "cp #{shared_path}/localsettings.php #{latest_release}/engine/localsettings.php"
    end
end
