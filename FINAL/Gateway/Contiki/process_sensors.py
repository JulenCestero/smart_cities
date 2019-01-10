#!/usr/bin/python
import serial 
import MySQLdb
 
#establish connection to MySQL. You'll have to change this for your database.
#dbConn = MySQLdb.connect("localhost","database_username","password","database_name") or die ("could not connect to database")
dbConn = MySQLdb.connect("localhost","admin","Admin_Smart-cities4","Hogwarts") or die ("could not connect to database")
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
   pieces = data.split(".")  #split the data by the tab
   print "pieces[0]=",pieces[0]
   print "pieces[1]=",pieces[1]
   print "pieces[2]=",pieces[2]
   #Here we are going to insert the data into the Database
   try:
    #  cursor.execute("INSERT INTO sensors (mote,light,temperature) VALUES (%s,%s,%s)", (pieces[0],pieces[1],pieces[2]))
    #  dbConn.commit() #commit the insert
    #  cursor.close()  #close the cursor
   except MySQLdb.IntegrityError:
     print "failed to insert data"
   finally:
     cursor.close()  #close just incase it failed
 except:
   print "Failed to get data from Sky mote!"