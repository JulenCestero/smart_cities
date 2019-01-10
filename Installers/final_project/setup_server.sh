# Install LAMP + Phpmyadmin
sudo apt-get install mysql-server
sudo mysql_secure_installation # Seleccionar y, 1, pass: Smart-cities4, y, y, y, y, y, 
sudo apt-get install phpmyadmin # Seleccionar apache, yes, Smart-cities4
sudo echo 'Include /etc/phpmyadmin/apache.conf' >> /etc/apache2/apache2.conf
sudo service apache2 restart
# Install Python3
sudo apt-get install python3
sudo apt-get install ipython # Optional
sudo apt-get install python3-pip
sudo apt-get install python-pip
# Install Motion
sudo apt-get install motion # https://www.maketecheasier.com/setup-motion-detection-webcam-ubuntu/
cd ~
mkdir .motion
sudo cp /etc/motion/motion.conf ~/.motion/motion.conf # Create the config file for Motion
# Install Ffmpeg
sudo add-apt-repository ppa:jonathonf/ffmpeg-4
sudo apt-get update
sudo apt-get install ffmpeg
# Install Grafana
wget https://dl.grafana.com/oss/release/grafana_5.4.2_amd64.deb
sudo dpkg -i grafana_5.4.2_amd64.deb
sudo service grafana-server start # For starting
# Extra
sudo apt-get install libmysqlclient-dev




pip install -r requirements.txt