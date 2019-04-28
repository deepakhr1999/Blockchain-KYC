# Blockchain-KYC
KYC involves a business to verify sensitive details of its prospective customer. This process is almost always done offline, person to person to ensure absolute security. We can ensure unshakeable security using blockchain. Built on the hyperledger fabric framework, this applicaton ensures the permissions for access granted to each bank. 

### Installation
#### 1. Install Docker (v17.03.1-ce or higher)
```sh
# Setup the repository
$ sudo apt-get update
$ sudo apt-get install apt-transport-https ca-certificates curl gnupg-agent software-properties-common
$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
$ sudo apt-get update
$ sudo apt-get install docker-ce docker-ce-cli containerd.io

# To access as non-root user
$ sudo groupadd docker
$ sudo usermod -aG docker $USER
$ sudo usermod -aG docker www-data
$ docker run hello-world
```

#### 2. Install docker-compose (v1.9.0 or higher)
```sh
$ sudo apt install docker-compose
$ docker --version && docker-compose --version
# Make sure that you are running Docker version 17.03.1-ce, Docker Compose version 1.9.0 or higher
```

#### 3. Install Golang (v1.8 or higher required)
```sh
$ cd ~/Downloads
$ sudo curl -O https://dl.google.com/go/go1.12.4.linux-amd64.tar.gz
$ sudo tar -xvf go1.12.4.linux-amd64.tar.gz
$ sudo mv go /usr/local
$ echo 'export PATH=$PATH:/usr/local/go/bin' >> ~/.profile
$ source ~/.profile
$ go version
```

#### 4. Install Hyperledger Samples, Binaries and Docker Images
```sh
$ curl -sSL http://bit.ly/2ysbOFE | bash -s
```

#### 5. Install LAMP stack
```sh
$ sudo apt install tasksel
$ sudo tasksel install lamp-server
```

#### 6. Install phpmyadmin
```sh
$ sudo apt-get install phpmyadmin php-mbstring php-gettext
$ sudo phpenmod mbstring
# Restart apache server
$ sudo systemctl restart apache2
```

#### 7. Clone this Repository at www/html
```
$ cd /var/www/html
$ git clone https://github.com/deepakhr1999/Blockchain-KYC
$ sudo chown -R $USER Blockchain-KYC
$ sudo chown -R www-data Blockchain-KYC
```
*** We need to update this with more instructions **
