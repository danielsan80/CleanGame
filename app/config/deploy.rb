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

set :shared_children, ["data", "vendor", "app/config"]
set :shared_files,    ["app/config/config.yml"]


after "deploy:symlink" do
    run "cp #{shared_path}/app/config/config.yml #{shared_path}/app/config/config.yml~"
    run "cp -a #{release_path}/app/config/config.yml.dist #{shared_path}/app/config/config.yml"
    run "mv #{shared_path}/app/config/config.yml~ #{shared_path}/app/config/config.yml"
    run "rm -Rf #{release_path}/app/config/config.yml"
    run "ln -nfs #{shared_path}/app/config/config.yml #{release_path}/app/config/config.yml"

    run "rm -Rf #{release_path}/data"
    run "ln -nfs #{shared_path}/data #{release_path}/data"

    run "ln -nfs #{shared_path}/vendor #{release_path}/vendor"

    run "cd #{release_path} && ./composer.phar install"
end

server "cleangame.danilosanchi.net", :app