import os
import time
import datetime
import math, os
import boto
from filechunkio import FileChunkIO

DB_HOST = 'localhost'
DB_USER = 'root'
DB_USER_PASSWORD = '1234'
DB_NAME = 'jarvis'
BACKUP_PATH = '/backup/dbbackup/'

TODAYBACKUPPATH = BACKUP_PATH + "respaldo"

print "creando carpeta"
if not os.path.exists(TODAYBACKUPPATH):
    os.makedirs(TODAYBACKUPPATH)

print "Buscando base de datos existente."
if os.path.exists(DB_NAME):
    file1 = open(DB_NAME)
    multi = 1
    print "DB no encontrada..."
    print "creando copia de seguridad de todas las BD " + DB_NAME
else:
    print "DB no encontrada"
    print "creando copia de seguridad " + DB_NAME
    multi = 0

if multi:
   in_file = open(DB_NAME,"r")
   flength = len(in_file.readlines())
   in_file.close()
   p = 1
   dbfile = open(DB_NAME,"r")

   while p <= flength:
       db = dbfile.readline()   # reading database name from file
       db = db[:-1]         # deletes extra line
       dumpcmd = "mysqldump -u " + DB_USER + " -p" + DB_USER_PASSWORD + " " + db + " > " + TODAYBACKUPPATH + "/" + db + ".sql"
       os.system(dumpcmd)
       p = p + 1
   dbfile.close()
else:
   db = DB_NAME
   dumpcmd = "mysqldump -u " + DB_USER + " -p" + DB_USER_PASSWORD + " " + db + " > " + TODAYBACKUPPATH + "/" + db + ".sql"
   os.system(dumpcmd)

print "copia de seguridad completada"
print "Con el nombre: '" + TODAYBACKUPPATH + "/" + DB_NAME +".sql' directory"

conn = boto.connect_s3(
	'AKIAJLXU6MQ62S62Q7TA', 
	'rpmYstAB2AZm3d5NIgFE3HuqC+K6pm4VN5XCGwby'
)
bucket = conn.get_bucket(
	'gofeels-props-images', 
	validate=True
)

source_path = TODAYBACKUPPATH + "/" + DB_NAME +".sql"
source_size = os.stat(source_path).st_size

mp = bucket.initiate_multipart_upload(
	os.path.basename(
		source_path
	)
)

chunk_size = 52428800
chunk_count = int(
	math.ceil(
		source_size / float(chunk_size)
	)
)

for i in range(chunk_count):
	offset = chunk_size * i
	print "\n\nEn proceso......"
	bytes = min(chunk_size, source_size - offset)
	with FileChunkIO(
		source_path, 
		'r', 
		offset=offset,
		bytes=bytes
	) as fp:
		mp.upload_part_from_file(
			fp, 
			part_num=i + 1
		)	

mp.complete_upload()
print "\nTERMINADO! " + "Peso: " + str(float(float(source_size) / 1000000.0)) + "MB Fecha:" + time.strftime('%m/%d/%Y-%H:%M:%S') + "\n"
print "\nArchivo en amazon: https://s3-sa-east-1.amazonaws.com/gofeels-props-images/" + DB_NAME +".sql\n"