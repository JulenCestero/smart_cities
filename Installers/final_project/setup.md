# Steps
1. Setup the gateway introducing the following commands in your terminal:
```
cd ~/smart_cities/Installers/final_project
sh setup_gateway.sh
pip install -r requirements.txt
```
2. Start the gateway services through:
```
cd ~/smart_cities/FINAL/GW
sh initSoftapd_videocamara.sh
sh QoS_nuevo.sh
sudo chmod 777 /dev/ttyUSB0
python ~/smart_cities/FINAL/Gateway/Contiki/process_sensors.py
```

3. Setup the server introducing the following commands in your terminal:
```
cd ~/smart_cities/Installers/final_project
sh setup_gateway.sh
pip install -r requirements.txt
cd ~/smart_cities/FINAL/Servidor
docker-compose up
```
