set :application, "cleangame"
set :repository,  "git@github.com:danielsan80/CleanGame.git"
set :serverName, "sg111.servergrove.com" # The server's hostname

set :scm, :git
set :domain,      "cleangame.danilosanchi.net"
set :deploy_to,   "/var/www/vhosts/danilosanchi.net/subdomains/cleangame/httpdocs/"

set :deploy_via,      :rsync_with_remote_cache
set :user,       "danilosa"
ssh_options[:port] = 22123

set  :keep_releases,  3

set  :use_sudo,      false

set :copy_exclude, [".git", ".DS_Store", ".gitignore", ".gitmodules"]

set :shared_children, ["data", "vendor"]
set :shared_files,    ["app/config/config.yml"]


after "deploy:symlink" do
    run "#{shared_path}/composer.phar update"
    run "ln -nfs #{shared_path}/data #{release_path}/data"
    run "ln -nfs #{shared_path}/vendor #{release_path}/vendor"
    run "ln -nfs #{shared_path}/app/config/config.yml #{release_path}/app/config/config.yml"
    run "chmod 777 #{shared_path}/data"
    run "chmod -R 777 #{shared_path}/cache"
end

server "cleangame.danilosanchi.net", :app