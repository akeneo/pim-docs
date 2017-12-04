set :stage, :staging

set :deploy_to, '/home/akeneo/'
set :ssh_user, 'akeneo'
server 'akeneo.tld', user: fetch(:ssh_user), roles: %w{web app db}
