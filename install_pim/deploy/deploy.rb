set :application, 'acme'
set :repo_url, 'git@github.com:acme/project.git'

set :format, :pretty
set :log_level, :info
set :symfony_env,  "staging"
set :symfony_directory_structure, 3
set :sensio_distribution_version, 4
set :app_path, "app"
set :web_path, "web"

set :app_config_path, "app/config"
set :log_path, "var/logs"
set :cache_path, "var/cache"

set :branch, ENV.fetch("BRANCH", "master")
set :composer_install_flags, '--no-dev --prefer-dist --no-interaction --optimize-autoloader'

set :linked_files, %w{app/config/parameters.yml}
set :linked_dirs, %w{var/logs web/uploads}

set :controllers_to_clear, ["app_*.php"]

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

set :keep_releases, 3

after 'deploy:finishing', 'deploy:cleanup'
after 'deploy:updated', 'symfony:assets:install'

namespace :deploy do
    desc "Regenerating cache, assets and javascript"
    task :javascript do
        on roles(:app) do
            within release_path do
                execute *%w[ rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/* ]
                symfony_console('pim:installer:assets', '--env=prod')
                symfony_console('cache:warmup', '--env=prod')
                execute *%w[ rm -rf node_modules; npm install; npm run ]
                execute *%w[ yarn run webpack ]
            end
        end
    end
end

after 'deploy:updated', 'deploy:javascript'
