#!/usr/bin/env bash

#== Import script args ==

timezone=$(echo "$1")

#== Bash helpers ==

function info {
  echo " "
  echo "--> $1"
  echo " "
}

#== Provision script ==

info "Provision-script user: `whoami`"

export DEBIAN_FRONTEND=noninteractive

info "Configure timezone"
timedatectl set-timezone ${timezone} --no-ask-password

info "Prepare root password for MySQL"
debconf-set-selections <<< "mysql-community-server mysql-community-server/root-pass password \"''\""
debconf-set-selections <<< "mysql-community-server mysql-community-server/re-root-pass password \"''\""
echo "Done!"

info "Add PHP 7.1 reposytory"
add-apt-repository ppa:ondrej/php -y

info "Add Oracle JDK repository"
add-apt-repository ppa:webupd8team/java -y

info "Add ElasticSearch sources"
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
echo "deb https://artifacts.elastic.co/packages/5.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-5.x.list

info "Update OS software"
apt-get update
apt-get upgrade -y

info "Install additional software"
apt-get install -y php7.1-curl php7.1-cli php7.1-intl php7.1-mysqlnd php7.1-gd php7.1-fpm php7.1-mbstring php7.1-xml php7.1-xdebug unzip nginx mysql-server-5.7 mc php7.1-zip php7.1-mcrypt php7.1-memcached php7.1-soap memcached npm

info "Install Oracle JDK"
debconf-set-selections <<< "oracle-java8-installer shared/accepted-oracle-license-v1-1 select true"
debconf-set-selections <<< "oracle-java8-installer shared/accepted-oracle-license-v1-1 seen true"
apt-get install -y oracle-java8-installer

info "Install ElasticSearch"
apt-get install -y elasticsearch
sed -i 's/-Xms2g/-Xms64m/' /etc/elasticsearch/jvm.options
sed -i 's/-Xmx2g/-Xmx64m/' /etc/elasticsearch/jvm.options
service elasticsearch restart

info "Install Redis"
apt-get install -y redis-server

info "Install Supervisor"
apt-get install -y supervisor

info "Configure MySQL"
sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
mysql -uroot <<< "CREATE USER 'root'@'%' IDENTIFIED BY ''"
mysql -uroot <<< "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%'"
mysql -uroot <<< "DROP USER 'root'@'localhost'"
mysql -uroot <<< "FLUSH PRIVILEGES"
echo "Done!"

info "Configure PHP-FPM"
sed -i 's/user = www-data/user = vagrant/g' /etc/php/7.1/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php/7.1/fpm/pool.d/www.conf
sed -i 's/owner = www-data/owner = vagrant/g' /etc/php/7.1/fpm/pool.d/www.conf
echo "Done!"

info "Configure NGINX"
sed -i 's/user www-data/user vagrant/g' /etc/nginx/nginx.conf
echo "Done!"

info "Configure Xdebug"
echo '
xdebug.remote_enable = 1
xdebug.remote_autostart=1
xdebug.default_enable = Off
xdebug.var_display_max_depth=6
xdebug.remote_handler="dbgp"
xdebug.remote_host=10.0.2.15
xdebug.remote_mode=req
xdebug.remote_connect_back = 1
xdebug.show_error_trace = 1
xdebug.remote_port = 9002
xdebug.idekey = PHPSTORM
xdebug.remote_log=/tmp/xdebug.log
xdebug.max_nesting_level = 512
xdebug.overload_var_dump=1
xdebug.show_local_vars=1
xdebug.profiler_output_dir=/tmp/profiler
xdebug.profiler_enable_trigger=0
xdebug.profiler_enable=0
xdebug.file_link_format = phpstorm://open?%f:%l'  | tee -a /etc/php/7.1/mods-available/xdebug.ini
echo 'export XDEBUG_CONFIG="idekey=PHPSTORM"' | tee -a /home/vagrant/.profile
echo "Done!"

info "Enabling site configuration"
ln -s /app/vagrant/nginx/app.conf /etc/nginx/sites-enabled/app.conf
echo "Done!"

info "Initailize databases for MySQL"
mysql -uroot <<< "CREATE DATABASE homestead"
mysql -uroot <<< "CREATE DATABASE homestead_test"
echo "Done!"

info "Enabling supervisor processes"
ln -s /app/vagrant/supervisor/queue.conf /etc/supervisor/conf.d/queue.conf
echo "Done!"

info "Install composer"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
