----mongo--backup---restore---
mongodump -d portal_log -o /home/backup/mongodb/dev
mongorestore --noIndexRestore -d portal_log_dev /data/backup/mongodb/dev/portal_log
----mongo--backup---restore---

--------php module-----------------------------------------

ImageMagick{
yum install ImageMagick
yum install ImageMagick-devel
pecl install imagick
}

-----------------------------------------------------------------------------------------------------------------------------------------------------------
Soap-client{
yum --enablerepo=webtatic install php-soap
rpm --import http://repo.webtatic.com/yum/RPM-GPG-KEY-webtatic-andy
yum --enablerepo=webtatic install  webtatic-release
}

-----------------------------------------------------------------------------------------------------------------------------------------------------------
Redis-php{
cd /usr/src
wget https://github.com/phpredis/phpredis/archive/master.zip -O phpredis.zip
unzip -o /usr/src/phpredis.zip && mv /usr/src/phpredis-* /usr/src/phpredis && cd /usr/src/phpredis && phpize && ./configure && make && sudo make install
sudo touch /etc/php.d/redis.ini && echo extension=redis.so > /etc/php.d/redis.ini
}

-----------------------------------------------------------------------------------------------------------------------------------------------------------
memcached{
yum install cyrus-sasl-devel zlib-devel gcc-c++
wget https://launchpad.net/libmemcached/1.0/1.0.16/+download/libmemcached-1.0.16.tar.gz
tar -xvf libmemcached-1.0.16.tar.gz
cd libmemcached-1.0.16
./configure --disable-memcached-sasl
make
make install
pecl install memcached
echo "extension=memcached.so" > /etc/php.d/memcached.ini

https://gist.github.com/paul91/11538376
}

-----------------------------------------------------------------------------------------------------------------------------------------------------------
gearman{
https://hasin.me/2013/10/30/installing-gearmand-libgearman-and-pecl-gearman-from-source/
}

Mongo-php{

http://www.liquidweb.com/kb/how-to-install-the-mongodb-php-driver-extension-on-centos-6/
}