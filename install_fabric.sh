echo "Installing Docker!"
sudo apt-get update
sudo apt-get install apt-transport-https ca-certificates curl gnupg-agent software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io
sudo groupadd docker
sudo usermod -aG docker $USER
sudo usermod -aG docker www-data
docker run hello-world


echo "Installing Docker-compose"
sudo apt install docker-compose
docker --version && docker-compose --version
# Make sure that you are running Docker version 17.03.1-ce, Docker Compose version 1.9.0 or higher

echo "Installing Golang v12.4"
cd ~/Downloads
sudo curl -O https://dl.google.com/go/go1.12.4.linux-amd64.tar.gz
sudo tar -xvf go1.12.4.linux-amd64.tar.gz
sudo mv go /usr/local
echo 'export PATH=$PATH:/usr/local/go/bin' >> ~/.profile
source ~/.profile
go version
cd -

echo "Installing Fabric"
curl -sSL http://bit.ly/2ysbOFE | bash -s

echo "Install MySql"
sudo apt-get update
sudo apt-get install mysql-server
mysql_secure_installation
systemctl status mysql.service
sudo apt-get install php7.2-mysql

echo "--------------------------------
edit /etc/php/7.2/apache2/php.ini file add these 3 lines
extension=mysql.so
extension=mysqli.so
extension=pdo_mysql.so
-----------------------------------
Then type the following command:
sudo phpenmod mysqli";