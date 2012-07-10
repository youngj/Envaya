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

ssh_key_path = "/cygwin/home/#{ENV['USERNAME']}/.ssh/id_rsa"

set :ssh_key_path, ssh_key_path

res = Net::SSH::KeyFactory.load_private_key(ssh_key_path)
ssh_options[:key_data] = [res.to_s]

role :web, "web1.envaya.org", "web2.envaya.org"
role :app, "web1.envaya.org", "web2.envaya.org"
role :db,  "web1.envaya.org", "web2.envaya.org"
role :cron, "web2.envaya.org"
role :queue, "web1.envaya.org", "web2.envaya.org"
role :search, "web1.envaya.org", "web2.envaya.org"
role :qworker, "web1.envaya.org", "web2.envaya.org"
role :memcached, "web1.envaya.org", "web2.envaya.org"

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
        iptables_setup
        php_setup
        dataroot_setup
        postfix_setup
    end
    
    task :ssh_key_setup do
        run "mkdir -p ~/.ssh"
        run "chmod 700 ~/.ssh"
        run "touch ~/.ssh/authorized_keys"
        top.upload("#{ssh_key_path}.pub", "/tmp/ssh_key.pub")
        run "cat /tmp/ssh_key.pub >> ~/.ssh/authorized_keys"
    end    
    
    task :update_test do
        test_dir = "#{deploy_to}/test"
        
        run "rsync -az --delete --exclude='config/local.php' --chmod=u+rx,g+rx,o+rx #{current_path}/ #{test_dir}"
        run "cp #{shared_path}/local_test.php #{test_dir}/config/local.php"
    end
        
    # sets up a single server with all of envaya's services installed
    task :full_setup do
        basic_setup
        web_setup
        db_setup
        rabbitmq_setup
        qworker_setup
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
        rabbitmq_setup
        qworker_setup
        memcached_setup
        sphinx_setup
    end
    
    task :sanity_check do
    
        files = [
            "build/cache.php",
            "build/path_cache.php",
            "www/_media/jquery-1.6.2.min.js",
            "www/_media/inline/header.js",
            "www/_media/inline/xhr.js",
            "www/_media/inline/dom.js",
            "www/_media/inline/language.js",
        ]
    
        files.each do |file|
            if !File.file?(File.join(Dir.pwd, file))
                throw "sanity check failed, missing #{file}; run make.php"
            end
        end
        
        print "sanity check passed"
    end
    
    task :localsettings_setup do    
        top.upload(File.join(Dir.pwd, "config/production.php"), "#{shared_path}/local.php")
    end
    
    task :dkim_key_setup do
        key_path = "/etc/mail/dkim.key"
        top.upload(File.join(Dir.pwd, "../envaya_ssl/dkim.key"), key_path)
        run "chmod 600 #{key_path}"        
        run "/etc/init.d/opendkim restart"
        run "/etc/init.d/postfix restart"
    end
    
    task :web_cert_setup, :roles => :web do
        cert_dir = "/etc/nginx/ssl"
        cert_path = "#{cert_dir}/envaya_combined.crt"
        key_path = "#{cert_dir}/envaya.org.key"
    
        top.upload(File.join(Dir.pwd, "../envaya_ssl/envaya.org.key"), key_path)
        run "chmod 400 #{key_path}"

        top.upload(File.join(Dir.pwd, "../envaya_ssl/envaya_combined.crt"), cert_path)            
        run "chmod 644 #{cert_path}"
        
        begin
            run "stat -t /etc/nginx/sites-enabled/ssl"
        rescue Exception    
            run "ln -s /etc/nginx/sites-available/ssl /etc/nginx/sites-enabled/ssl"
            run "nginx -t"
            run "/etc/init.d/nginx reload"
        end
    end
    
    task :iptables_setup do
        run "#{current_path}/scripts/setup/iptables.sh"
    end        
    
    task :php_setup do
        run "#{current_path}/scripts/setup/php.sh"
    end
    
    task :memcached_setup do
        run "#{current_path}/scripts/setup/memcached.sh"
    end    
    
    task :db_setup, :roles => :db do
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
    
    task :postfix_setup do
        run "#{current_path}/scripts/setup/postfix.sh"
    end
    
    task :dataroot_setup do
        run "cd #{current_path} && php scripts/install_dataroot.php"
    end
    
    task :web_setup, :roles => :web do
        run "#{current_path}/scripts/setup/nginx.sh"
    end
        
    task :upgrade do
        run "#{current_path}/scripts/setup/upgrade.sh"
    end
    
    task :rabbitmq_setup, :roles => :queue do
        # todo rsync kestrel .jar and libs
        run "#{current_path}/scripts/setup/rabbitmq.sh"
    end
    
    task :sphinx_setup, :roles => :search do
        run "#{current_path}/scripts/setup/sphinx.sh"        
        run "#{current_path}/scripts/setup/sphinx_service.sh"        
    end

    task :extras_setup do
        run "#{current_path}/scripts/setup/extras.sh"
    end
    
    task :qworker_setup, :roles => :qworker do
        run "#{current_path}/scripts/setup/qworker.sh"
    end 
    
    task :cron_setup, :roles => :cron do
        run "#{current_path}/scripts/setup/cron.sh"
    end
        
    task :backup_db, :roles => :app, :except => { :no_release => true } do            
        run "cd #{current_path} && php scripts/backup.php"
    end    

    task :finalize_update, :except => { :no_release => true } do
        run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
        publish_settings       
    end
    
    task :create_symlink, :except => { :no_release => true } do   
        run "rm -f #{current_path} && ln -s #{latest_release} #{current_path}"
        update_nginx_symlink
    end    
    
    task :update_nginx_symlink, :roles => :app do
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
    
    task :update_translations, :roles => :cron do
        run "php #{current_path}/mod/translate/scripts/update_translations.php"
    end
    
    task :restart_cron, :roles => :cron do        
        run "/etc/init.d/phpCron restart"
    end
    
    task :restart_nginx, :roles => :web do
        run "nginx -t"
        run "/etc/init.d/nginx reload"
        run "rm -rf /var/nginx/cache/envaya"
    end
    
    task :restart_qworkers, :roles => :qworker do
        run "/etc/init.d/qworkers restart"
    end
    
    task :restart, :roles => :app, :except => { :no_release => true } do        
        restart_cron
        restart_nginx
        restart_qworkers
    end

    task :update_iptables do
        top.upload(File.join(Dir.pwd, "scripts/setup/conf/iptables.fw"), "/root/iptables.fw")
        run "iptables-restore < /root/iptables.fw"
    end    
    
    task :update_settings do
        localsettings_setup
        publish_settings
        restart
    end    
    
    task :publish_settings do
        run "cp #{shared_path}/local.php #{latest_release}/config/local.php"                   
    end
end
