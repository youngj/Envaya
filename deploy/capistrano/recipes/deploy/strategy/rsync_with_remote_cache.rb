#
# Based on https://github.com/vigetlabs/capistrano_rsync_with_remote_cache/tree, 
# but removed local cache and all dependencies on source control, to deploy
# directly from the local working directory.
# -------------------------------------------------
#
# Copyright © 2007 - 2010 Patrick Reagan (patrick.reagan@viget.com) & Mark Cornick
# 
# Permission is hereby granted, free of charge, to any person obtaining a copy of 
# this software and associated documentation files (the "Software"), to deal in the 
# Software without restriction, including without limitation the rights to use, copy,
# modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
# and to permit persons to whom the Software is furnished to do so, subject to the 
# following conditions:
# 
# The above copyright notice and this permission notice shall be included in all 
# copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
# INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
# PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
# HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
# CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE 
# OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
#

require 'capistrano/recipes/deploy/strategy/remote'
require 'fileutils'

module Capistrano
  module Deploy
    module Strategy
      class RsyncWithRemoteCache < Remote
        
        def self.default_attribute(attribute, default_value)
          define_method(attribute) { configuration[attribute] || default_value }
        end
        
        default_attribute :rsync_options, '-azv --delete --delete-excluded --chmod=u+rx,g+rx,o+rx --super'
        default_attribute :local_path, '.'
        default_attribute :repository_cache, 'cached-copy'

        def deploy!
          update_remote_cache
          copy_remote_cache
        end        
        
        def update_remote_cache
          finder_options = {:except => { :no_release => true }}
          run("chown -R root:root #{repository_cache_path}")
          find_servers(finder_options).each do |s| 
            # TODO pass the password to rsync (e.g. with expect, or ruby rsync library) to avoid double prompting
            res = system(rsync_command_for(s)) 
            if !res
                throw :rsync_failed
            end
          end
          run("chown -R root:root #{repository_cache_path}")
        end
        
        def copy_remote_cache
          run("rsync -a --delete #{repository_cache_path}/ #{configuration[:release_path]}/")
        end
        
        def rsync_command_for(server)        
          exclude = configuration[:copy_exclude].map {|filename| "--exclude=\"#{filename}\""}.join(" ")        
          "rsync #{rsync_options} #{exclude} --rsh='ssh -p #{ssh_port(server)}' #{local_path}/ #{rsync_host(server)}:#{repository_cache_path}/"
        end
        
        def repository_cache_path
          File.join(shared_path, repository_cache)
        end
        
        def ssh_port(server)
          server.port || ssh_options[:port] || 22
        end
                
        def rsync_host(server)
          configuration[:user] ? "#{configuration[:user]}@#{server.host}" : server.host
        end        

        # Defines commands that should be checked for by deploy:check. 
        def check!
          super.check do |check|
            check.local.command('rsync')
            check.remote.command('rsync')
          end
        end

      end
      
    end
  end
end
