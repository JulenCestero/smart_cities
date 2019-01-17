#!/usr/bin/python
import serial 
import MySQLdb
import time

def check_data(pieces):
  for piece in pieces:
    try:
      int (piece)
    except:
      return False
  return True
#establish connection to MySQL. You'll have to change this for your database.
#dbConn = MySQLdb.connect("localhost","database_username","password","database_name") or die ("could not connect to database")
dbConn = MySQLdb.connect("192.168.4.118","contiki","Contiki_Smart-cities4","Hogwarts") or die ("could not connect to database")
#open a cursor to the database
#cursor = dbConn.cursor()
 
#device = '/dev/tty.usbmodem1411' #this will have to be changed to the serial port you are using
device = '/dev/ttyUSB0' #this will have to be changed to the serial port you are using
try:
  print "Trying...",device 
  skyMote = serial.Serial(device, 115200) 
except: 
  print "Failed to connect on",device    
 
while True:
 #open a cursor to the database
  cursor = dbConn.cursor() 
  try:  
    data = skyMote.readline()  #read the data from the sky Mote
    print data
    pieces = data[:-1].split(".")  #split the data by the tab
    if not check_data(pieces):
      continue
    print "pieces[0]=",pieces[0]
    print "pieces[1]=",pieces[1]
    print "pieces[2]=",pieces[2]
    print "pieces[3]=",pieces[3]
    print "pieces[4]=",pieces[4]
    
    #  skyMote.write("9\n")
    #  skyMote.write("1\n")
    #  skyMote.write("0\n")	
    #Here we are going to insert the data into the Database
    try:
      cursor.execute("INSERT INTO sensors (mote,light,temperature, mode, n_leds) VALUES (%s,%s,%s,%s,%s)", (pieces[0],pieces[1],pieces[2],pieces[3],pieces[4]))
      dbConn.commit() #commit the insert
    except MySQLdb.IntegrityError:
      print "failed to insert data"
    finally:
      cursor.close()
    try:
      cursor = dbConn.cursor() 
      cursor.execute("SELECT mode, n_leds, mote FROM sensor_status")
      if len(list(cursor)) != 3:
        continue
      for mode, n_leds, mote in cursor:
        tmp = str(mote) + '.' + str(mode) + '.' + str(n_leds) + '\n'
        print tmp
        skyMote.write(tmp)
        time.sleep(1)
    except Exception as e:
      print e
    finally:
      cursor.close()
  except Exception as e:
    print 'MAL: ' + str(e)
    print 'Data: ' + data[:-1]
